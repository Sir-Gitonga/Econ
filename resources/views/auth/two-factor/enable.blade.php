@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>🔐 Enable Two-Factor Authentication</h1>
        <p>Add an extra layer of security to your account</p>
    </div>

    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200);">
            <div style="background: #dbeafe; border: 1px solid #93c5fd; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-info-circle" style="color: #0369a1; font-size: 1.2rem; margin-top: 0.25rem; flex-shrink: 0;"></i>
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #0c4a6e; font-weight: 600;">What is Two-Factor Authentication?</p>
                        <p style="margin: 0; color: #0c4a6e; font-size: 0.95rem;">
                            Two-factor authentication adds an extra layer of security to your account. In addition to your password, you'll need to enter a verification code sent to your phone each time you log in.
                        </p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('user.two-factor.enable') }}" method="POST">
                @csrf

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="phone" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--gray-900);">
                        Phone Number *
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form-control @error('phone') is-invalid @enderror" 
                        pattern="^(\+?254|0)[1-9]\d{8}$"
                        placeholder="e.g., 0712345678 or +254712345678" 
                        value="{{ old('phone', auth()->user()->mobile ?? '') }}"
                        required
                        autofocus
                    >
                    <small style="color: var(--gray-500); display: block; margin-top: 0.5rem;">
                        <i class="fas fa-phone"></i>
                        Kenyan phone numbers only (e.g., 0712345678 or +254712345678)
                    </small>
                    @error('phone')
                        <span style="color: #dc2626; display: block; margin-top: 0.5rem;">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    <i class="fas fa-lock"></i>
                    <span>Continue</span>
                </button>
            </form>

            <a href="{{ route('user.account') }}" class="btn btn-secondary" style="width: 100%;">
                <i class="fas fa-arrow-left"></i>
                <span>Cancel</span>
            </a>
        </div>

        <div style="background: #f3f4f6; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem;">
            <h4 style="margin: 0 0 1rem 0; color: var(--gray-900); font-size: 0.95rem;">
                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                Benefits of Two-Factor Authentication
            </h4>
            <ul style="margin: 0; padding-left: 1.5rem; color: var(--gray-600); font-size: 0.9rem;">
                <li style="margin-bottom: 0.5rem;">Protects your account from unauthorized access</li>
                <li style="margin-bottom: 0.5rem;">Adds security even if your password is compromised</li>
                <li style="margin-bottom: 0.5rem;">Verification code expires in 5 minutes</li>
                <li>You can disable it anytime from your account settings</li>
            </ul>
        </div>
    </div>
@endsection
