@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add User</h3>
        </div>

        <div class="wg-box">
            <form action="{{ adminRoute('admin.users.store', ['subdomain' => Auth::user()->company->slug]) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Phone</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Role</label>
                    <select name="role" class="form-control">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="text-right">
                    <button class="tf-button style-1">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
