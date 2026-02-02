@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Users</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ adminRoute('admin.dashboard') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Users</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="mb-4 text-right">
                <a href="{{ adminRoute('admin.users.create') }}" class="tf-button style-1">Add User</a>
            </div>

            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{!! $user->status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Disabled</span>' !!}</td>
                            <td>{{ $user->last_login ? $user->last_login->diffForHumans() : '-' }}</td>
                            <td>
                                <a href="{{ adminRoute('admin.users.edit', ['subdomain' => Auth::user()->company->slug, 'user' => $user->id]) }}" class="btn btn-sm btn-primary">Edit</a>

                                <form action="{{ adminRoute('admin.users.toggle_status', ['subdomain' => Auth::user()->company->slug, 'user' => $user->id]) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm btn-{{ $user->status ? 'warning' : 'success' }}">{{ $user->status ? 'Disable' : 'Enable' }}</button>
                                </form>

                                <form action="{{ adminRoute('admin.users.reset_password', ['subdomain' => Auth::user()->company->slug, 'user' => $user->id]) }}" method="POST" style="display:inline" onsubmit="return confirm('Reset password for {{ addslashes($user->name) }}?')">
                                    @csrf
                                    <button class="btn btn-sm btn-secondary">Reset Password</button>
                                </form>

                                <form action="{{ adminRoute('admin.users.destroy', ['subdomain' => Auth::user()->company->slug, 'user' => $user->id]) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
