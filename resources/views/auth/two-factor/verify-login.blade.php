@extends('layouts.app')

@section('content')

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <div style="max-width: 500px; margin: 0 auto;">
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <i class="fas fa-mobile-alt" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1rem; display: block;"></i>
                    <h2 style="margin: 0 0 0.5rem 0; color: var(--gray-900);">Two-Factor Verification</h2>
                    <p style="margin: 0; color: var(--gray-600); font-size: 0.95rem;">
                        Enter the verification code sent to your phone
                    </p>
                </div>

                @if(session('info'))
                    <div style="background: #dbeafe; color: #0c4a6e; border: 1px solid #93c5fd; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                        <i class="fas fa-info-circle"></i>
                        {{ session('info') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                        <i class="fas fa-times-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('two-factor.verify-login') }}" method="POST" id="verificationForm">
                    @csrf

                    <div style="margin-bottom: 1.5rem;">
                        <label for="code" style="display: block; margin-bottom: 1rem; font-weight: 600; color: var(--gray-900); text-align: center;">
                            Enter 6-Digit Code
                        </label>
                        <input 
                            type="text" 
                            id="code" 
                            name="code" 
                            class="form-control @error('code') is-invalid @enderror" 
                            placeholder="000000" 
                            maxlength="6"
                            pattern="\d{6}"
                            inputmode="numeric"
                            value="{{ old('code') }}"
                            required
                            autofocus
                            style="text-align: center; font-size: 1.5rem; letter-spacing: 0.35em; font-weight: bold; padding: 1.5rem; border: 2px solid var(--gray-300); border-radius: 8px;"
                        >
                        @error('code')
                            <span style="color: #dc2626; display: block; margin-top: 0.5rem; text-align: center;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                        <input type="checkbox" id="remember" name="remember" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        <label for="remember" style="margin: 0; cursor: pointer; color: var(--gray-700);">
                            Remember this device
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="margin-bottom: 1rem; padding: 0.75rem;">
                        <i class="fas fa-check"></i>
                        <span>Verify & Login</span>
                    </button>
                </form>

                <button type="button" id="resendBtn" class="btn btn-secondary w-100" style="margin-bottom: 1rem; padding: 0.75rem; background-color: var(--gray-200); color: var(--gray-900); border: none;" onclick="resendCode()">
                    <i class="fas fa-redo"></i>
                    <span>Resend Code</span>
                </button>

                <a href="{{ route('login') }}" class="btn btn-link w-100" style="text-decoration: none; padding: 0.75rem;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Login</span>
                </a>

                <div style="background: #f3f4f6; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem; text-align: center;">
                    <p style="margin: 0; color: var(--gray-600); font-size: 0.9rem;">
                        <i class="fas fa-clock"></i>
                        Code expires in <strong id="timer">5:00</strong>
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    // Timer for 5 minutes
    let timeLeft = 300; // 5 minutes in seconds

    const updateTimer = () => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft > 0) {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        } else {
            document.getElementById('verificationForm').innerHTML += '<div class="alert alert-danger" style="margin-top: 1rem;">Code expired. Please request a new one.</div>';
            document.getElementById('code').disabled = true;
            document.getElementById('verificationForm').style.opacity = '0.5';
            document.getElementById('resendBtn').disabled = false;
        }
    };

    updateTimer();

    function resendCode() {
        const btn = document.getElementById('resendBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending...</span>';

        fetch('{{ route("two-factor.resend-code") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                timeLeft = 300; // Reset timer
                updateTimer();
                btn.innerHTML = '<i class="fas fa-redo"></i> <span>Resend Code</span>';
                btn.disabled = false;
            } else {
                alert(data.error || 'Failed to resend code');
                btn.innerHTML = '<i class="fas fa-redo"></i> <span>Resend Code</span>';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.innerHTML = '<i class="fas fa-redo"></i> <span>Resend Code</span>';
            btn.disabled = false;
        });
    }

    // Auto-submit form when 6 digits are entered
    document.getElementById('code').addEventListener('input', function() {
        if (this.value.length === 6) {
            // Optional: auto-submit after a short delay
            setTimeout(() => {
                document.getElementById('verificationForm').submit();
            }, 300);
        }
    });
</script>

@endsection
