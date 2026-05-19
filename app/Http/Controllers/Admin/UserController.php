<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of tenant users.
     */
    public function index($subdomain)
    {
        $users = User::orderBy('name')->paginate(25);

        return view('admin.users.index', compact('users', 'subdomain'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create($subdomain)
    {
        $company = auth()->user()->company;
        $roles = Role::where('company_id', $company->id)->orderBy('name')->get();

        return view('admin.users.create', compact('subdomain', 'roles'));
    }

    /**
     * Store a newly created user in database.
     */
    public function store($subdomain, StoreUserRequest $request)
    {
        $company = auth()->user()->company;

        $user = User::create([
            'company_id' => $company->id,
            'name'       => $request->validated('name'),
            'email'      => $request->validated('email'),
            'mobile'     => $request->validated('mobile'),
            'password'   => Hash::make($request->validated('password')),
            'status'     => true,
        ]);

        // Lookup role by id scoped to company and sync both FK and legacy string
        $role = Role::where('company_id', $company->id)
            ->where('id', $request->validated('role_id'))
            ->first();

        if ($role) {
            $user->role_id = $role->id;
            $user->role = $role->name;
            $user->save();
        }

        return redirect()->route('admin.users.index', ['subdomain' => $subdomain])
            ->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Show the form for editing a user.
     */
    public function edit($subdomain, User $user)
    {
        $company = auth()->user()->company;
        $roles = Role::where('company_id', $company->id)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'subdomain', 'roles'));
    }

    /**
     * Update the specified user in database.
     */
public function update($subdomain, UpdateUserRequest $request, User $user)
{
    // Update basic fields
    $data = $request->validated();

    $user->update([
        'name'   => $data['name'],
        'email'  => $data['email'],
        'mobile' => $data['mobile'] ?? null,
        'status' => array_key_exists('status', $data) ? (bool) $data['status'] : $user->status,
    ]);

    // Ensure role lookup is scoped to the current company (tenant)
    $company = auth()->user()->company;

    // UpdateUserRequest validates 'role_id' (id from the roles table)
    $role = Role::where('company_id', $company->id)
        ->where('id', $data['role_id'])
        ->first();

    if ($role) {
        // Sync both FK and legacy string column so getRoleName() and relations work
        $user->role_id = $role->id;
        $user->role = $role->name;
        $user->save();
    }

    // Redirect back to admin users index with success message
    return redirect()->route('admin.users.index', ['subdomain' => $subdomain])
        ->with('success', "User '{$user->name}' updated successfully.");
}


    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus($subdomain, User $user)
    {
        $user->update(['status' => !$user->status]);
        $status = $user->status ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index', ['subdomain' => $subdomain])
            ->with('success', "User '{$user->name}' has been {$status}.");
    }

    /**
     * Reset user password to a secure random password.
     */
    public function resetPassword($subdomain, User $user)
    {
        $temporaryPassword = Str::random(12);

        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return redirect()->route('admin.users.index', ['subdomain' => $subdomain])
            ->with('success', "Password reset. Temporary password: {$temporaryPassword}");
    }

    /**
     * Delete a user from the system.
     */
    public function destroy($subdomain, User $user)
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index', ['subdomain' => $subdomain])
            ->with('success', "User '{$name}' has been deleted.");
    }
}
