<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'mobile' => 'nullable|string|max:20|unique:users',
            'role' => 'required|in:admin,cashier,user',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'role' => $validated['role'] ?? 'user',
            'password' => Hash::make($validated['password']),
            'status' => 1,
        ]);

        return redirect(adminRoute('admin.users.index'))->with('success', 'User created successfully');
    }

    public function edit($user)
    {
        $user = $this->resolveUser($user);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $user)
    {
        $user = $this->resolveUser($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20|unique:users,mobile,' . $user->id,
            'role' => 'required|in:admin,cashier,user',
            'status' => 'nullable|boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? $user->mobile,
            'role' => $validated['role'],
            'status' => isset($validated['status']) ? (bool)$validated['status'] : $user->status,
        ]);

        return redirect(adminRoute('admin.users.index'))->with('success', 'User updated successfully');
    }

    public function toggleStatus($user)
    {
        $user = $this->resolveUser($user);
        $user->status = !$user->status;
        $user->save();

        return back()->with('success', 'User status updated');
    }

    public function resetPassword($user)
    {
        $user = $this->resolveUser($user);

        $new = 'Password123!';
        $user->password = Hash::make($new);
        $user->save();

        return back()->with('success', "Password reset to default: $new (advise user to change)");
    }

    public function destroy($user)
    {
        $user = $this->resolveUser($user);
        $user->delete();
        return back()->with('success', 'User deleted');
    }

    /**
     * Resolve a route parameter into a User model instance.
     * Accepts a User instance, array/object with an 'id', or a scalar id.
     */
    private function resolveUser($user): User
    {
        if ($user instanceof User) {
            return $user;
        }

        if (is_array($user) && isset($user['id'])) {
            return User::findOrFail($user['id']);
        }

        if (is_object($user) && isset($user->id)) {
            return User::findOrFail($user->id);
        }

        // scalar id (string/int)
        return User::findOrFail($user);
    }
}
