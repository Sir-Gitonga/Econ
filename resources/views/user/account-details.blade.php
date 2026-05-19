@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>🔐 Account Settings</h1>
        <p>Manage your profile information and security</p>
    </div>

    {{-- PROFILE CARD --}}
    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; padding: 2rem; border-radius: var(--radius-lg); margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem;">
        <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <h2 style="margin: 0 0 0.5rem 0;">{{ $user->name }}</h2>
            <p style="margin: 0; opacity: 0.9;">{{ $user->email }}</p>
            <small style="opacity: 0.8;">Member since {{ $user->created_at->format('M d, Y') }}</small>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        {{-- PERSONAL INFORMATION --}}
        <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200);">
            <h3 style="color: var(--gray-900); margin-bottom: 1.5rem; font-size: 1.1rem;">Personal Information</h3>

            @if(session('success'))
                <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('user.account.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span style="color: #dc2626; display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span style="color: #dc2626; display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="mobile">Phone Number</label>
                    <input type="tel" id="mobile" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? $lastOrderPhone ?? '') }}">
                    <small style="color: #6b7280; display: block; margin-top: 0.5rem;">
                        Using your saved phone number, or the phone from your most recent order if you don’t have one set.
                    </small>
                    @error('phone') <span style="color: #dc2626; display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </form>
        </div>

        {{-- CHANGE PASSWORD --}}
        <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200);">
            <h3 style="color: var(--gray-900); margin-bottom: 1.5rem; font-size: 1.1rem;">Change Password</h3>

            <form action="{{ route('user.account.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                    @error('current_password') <span style="color: #dc2626; display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                    @error('new_password') <span style="color: #dc2626; display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                </div>

                <small style="color: var(--gray-500); display: block; margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i>
                    Password must be at least 8 characters long
                </small>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-lock"></i>
                    <span>Update Password</span>
                </button>
            </form>
        </div>
    </div>

    {{-- ACCOUNT PREFERENCES --}}
    <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); margin-top: 2rem;">
        <h3 style="color: var(--gray-900); margin-bottom: 1.5rem; font-size: 1.1rem;">Preferences</h3>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <label style="display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span style="color: var(--gray-700);">Receive order updates via email</span>
            </label>

            <label style="display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                <span style="color: var(--gray-700);">Receive promotional offers and news</span>
            </label>
        </div>

        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
            <button class="btn btn-secondary" style="width: 100%;">
                <i class="fas fa-save"></i>
                <span>Save Preferences</span>
            </button>
        </div>
    </div>

    {{-- TWO-FACTOR AUTHENTICATION --}}
    <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); margin-top: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: var(--primary);"></i>
            <h3 style="color: var(--gray-900); margin: 0; font-size: 1.1rem;">Two-Factor Authentication</h3>
        </div>
        <p style="color: var(--gray-600); margin-bottom: 1.5rem;">Add an extra layer of security to your account by enabling two-factor authentication via SMS.</p>

        @if($user->two_factor_enabled)
            {{-- 2FA is ENABLED --}}
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <i class="fas fa-check-circle" style="color: #059669; font-size: 1.2rem;"></i>
                    <span style="color: #065f46; font-weight: 600;">Two-Factor Authentication is Enabled</span>
                </div>
                <p style="color: #047857; margin: 0; font-size: 0.9rem;">
                    <strong>Phone:</strong> {{ substr($user->two_factor_phone, 0, 3) }}***{{ substr($user->two_factor_phone, -2) }}
                </p>
            </div>

            <form action="{{ route('user.two-factor.disable') }}" method="POST" style="display: inline;">
                @csrf
                @method('POST')
                
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="password">Confirm Password to Disable 2FA</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                    @error('password') <span style="color: #dc2626; display: block; margin-top: 0.5rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <i class="fas fa-lock-open"></i>
                    <span>Disable Two-Factor Authentication</span>
                </button>
            </form>
        @else
            {{-- 2FA is DISABLED --}}
            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-exclamation-circle" style="color: #d97706; font-size: 1.2rem;"></i>
                    <span style="color: #92400e;">Two-Factor Authentication is currently disabled</span>
                </div>
            </div>

            <a href="{{ route('user.two-factor.enable') }}" class="btn btn-primary" style="width: 100%; display: inline-block; text-align: center;">
                <i class="fas fa-lock"></i>
                <span>Enable Two-Factor Authentication</span>
            </a>
        @endif
    </div>

    {{-- DANGER ZONE --}}
    <div style="background: var(--danger-light); padding: 2rem; border-radius: var(--radius-lg); border: 2px solid var(--danger); margin-top: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: var(--danger);"></i>
            <h3 style="color: var(--danger); margin: 0;">Danger Zone</h3>
        </div>
        <p style="color: #b91c1c; margin-bottom: 1rem;">These actions cannot be undone. Please be careful.</p>
        <button class="btn btn-secondary" style="width: 100%; background-color: var(--danger); color: white; border: none;">
            <i class="fas fa-trash"></i>
            <span>Delete Account</span>
        </button>
    </div>
@endsection
