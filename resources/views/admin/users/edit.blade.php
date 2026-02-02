@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit User</h3>
        </div>

        <div class="wg-box">
            <form action="{{ adminRoute('admin.users.update', ['subdomain' => Auth::user()->company->slug, 'user' => $user->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Phone</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Role</label>
                    <select name="role" class="form-control">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                        <option value="cashier" {{ $user->role === 'cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="label">Active</label>
                    <input type="checkbox" name="status" value="1" {{ $user->status ? 'checked' : '' }}>
                </div>

                <div class="text-right">
                    <button class="tf-button style-1">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
