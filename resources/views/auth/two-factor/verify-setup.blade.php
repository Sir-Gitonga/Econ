@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>✅ Verify Your Phone Number</h1>
        <p>Enter the verification code sent to your phone</p>
    </div>

    <div style="max-width: 500px; margin: 0 auto;">
        <div style="background: var(--white); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--gray-200);">
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-mobile-alt" style="color: #059669; font-size: 1.2rem; margin-top: 0.25rem; flex-shrink: 0;"></i>
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #065f46; font-weight: 600;">Verification code sent</p>
                        <p style="margin: 0; color: #065f46; font-size: 0.95rem;">
                            A 6-digit code has been sent to your phone. The code expires in 5 minutes.
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

            @if($errors->any())
                <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                    <i class="fas fa-times-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('user.two-factor.verify-setup') }}" method="POST" id="verificationForm">
                @csrf

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="code" style="display: block; margin-bottom: 0.75rem; font-weight: 600; color: var(--gray-900); text-align: center;">
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
                        style="text-align: center; font-size: 1.5rem; letter-spacing: 0.35em; font-weight: bold;"
                    >
                    @error('code')
                        <span style="color: #dc2626; display: block; margin-top: 0.5rem; text-align: center;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    <i class="fas fa-check"></i>
                    <span>Verify & Enable 2FA</span>
                </button>
            </form>

            <button type="button" id="resendBtn" class="btn btn-secondary" style="width: 100%; margin-bottom: 1rem;" onclick="resendCode()">
                <i class="fas fa-redo"></i>
                <span>Resend Code</span>
            </button>

            <a href="{{ route('user.account') }}" class="btn btn-link" style="width: 100%; text-decoration: none;">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
        </div>

        <div style="background: #f3f4f6; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem; text-align: center;">
            <p style="margin: 0; color: var(--gray-600); font-size: 0.9rem;">
                <i class="fas fa-clock"></i>
                Code expires in <strong id="timer">5:00</strong>
            </p>
        </div>
    </div>

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
                document.getElementById('verificationForm').innerHTML += '<div class="alert alert-danger">Code expired. Please request a new one.</div>';
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
