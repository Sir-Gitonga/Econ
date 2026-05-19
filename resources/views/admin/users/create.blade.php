@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Add User</h3>
        </div>

        <div class="wg-box">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <h4 class="alert-heading">Validation Errors</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ adminRoute('admin.users.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="label">Phone</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-control @error('mobile') is-invalid @enderror">
                    @error('mobile')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                    @error('password_confirmation')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-right">
                    <button class="tf-button style-1">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
