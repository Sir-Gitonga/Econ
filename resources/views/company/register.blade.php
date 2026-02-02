@extends('layouts.company')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); display: flex; align-items: center; justify-content: center; padding: 40px 20px;">
    <div class="wg-box">
        <header>
            <h2>Create Business Account</h2>
            <p>Join thousands of businesses already managing their operations with us</p>
        </header>

    <form method="POST" action="{{ route('company.store') }}" id="registrationForm">
        @csrf

        <!-- Stepper UI -->
        <div class="stepper-container">
            <div id="step1-tab" class="step-tab active">
                <span class="indicator">1</span>
                <span class="label">Email</span>
            </div>
            <div id="step2-tab" class="step-tab">
                <span class="indicator">2</span>
                <span class="label">Business</span>
            </div>
            <div id="step3-tab" class="step-tab">
                <span class="indicator">3</span>
                <span class="label">Location</span>
            </div>
            <div id="step4-tab" class="step-tab">
                <span class="indicator">4</span>
                <span class="label">Security</span>
            </div>
            <div id="step5-tab" class="step-tab">
                <span class="indicator">5</span>
                <span class="label">Finish</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar-bg">
                <div class="progress-bar" style="width: 20%"></div>
            </div>
        </div>

        <!-- STEP 1: Email -->
        <div class="step" id="step1">
            <label class="form-label">Email Address <span>*</span></label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
            @error('email')<span class="error-message">{{ $message }}</span>@enderror
            <div id="email-error" class="error-message hidden"></div>
            <p class="form-helper">We'll use this to send you important updates</p>

            <div class="button-group">
                <button type="button" onclick="goBack()" class="tf-button btn-back">
                    ← Back
                </button>
                <button type="button" onclick="validateAndNext(1)" class="tf-button style-1">
                    Next →
                </button>
            </div>
        </div>

        <!-- STEP 2: Business Details -->
        <div class="step hidden" id="step2">
            <label class="form-label">Business/Shop Name <span>*</span></label>
            <input id="company_name" class="form-control" type="text" name="company_name" value="{{ old('company_name') }}" required autocomplete="organization" />
            @error('company_name')<span class="error-message">{{ $message }}</span>@enderror
            <div id="company_name-error" class="error-message hidden"></div>

            <label class="form-label" style="margin-top: 18px;">Business Phone <span>*</span></label>
            <input id="phone" class="form-control" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="+254 00000000" />
            @error('phone')<span class="error-message">{{ $message }}</span>@enderror
            <div id="phone-error" class="error-message hidden"></div>

            <div class="button-group">
                <button type="button" onclick="nextStep(1)" class="tf-button btn-back">
                    ← Back
                </button>
                <button type="button" onclick="validateAndNext(2)" class="tf-button style-1">
                    Next →
                </button>
            </div>
        </div>

        <!-- STEP 3: Location -->
        <div class="step hidden" id="step3">
            <label class="form-label">Business Address <span>*</span></label>
            <input id="address" class="form-control" type="text" name="address" value="{{ old('address') }}" required />
            @error('address')<span class="error-message">{{ $message }}</span>@enderror
            <div id="address-error" class="error-message hidden"></div>

            <label class="form-label" style="margin-top: 18px;">City</label>
            <input id="city" class="form-control" type="text" name="city" value="{{ old('city') }}" />
            <div id="city-error" class="error-message hidden"></div>

            <label class="form-label" style="margin-top: 18px;">Country <span>*</span></label>
            <select id="country" name="country" class="form-control" required>
                <option value="">Select Country</option>
                <option value="US">United States</option>
                <option value="CA">Canada</option>
                <option value="UK">United Kingdom</option>
                <option value="AU">Australia</option>
                <option value="KE">Kenya</option>
                <option value="UG">Uganda</option>
                <option value="TZ">Tanzania</option>
                <option value="ZA">South Africa</option>
                <option value="NG">Nigeria</option>
                <option value="GH">Ghana</option>
                <option value="OTHER">Other</option>
            </select>
            @error('country')<span class="error-message">{{ $message }}</span>@enderror
            <div id="country-error" class="error-message hidden"></div>

            <div class="button-group">
                <button type="button" onclick="nextStep(2)" class="tf-button btn-back">
                    ← Back
                </button>
                <button type="button" onclick="validateAndNext(3)" class="tf-button style-1">
                    Next →
                </button>
            </div>
        </div>

        <!-- STEP 4: Security -->
        <div class="step hidden" id="step4">
            <label class="form-label">Password <span>*</span></label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
            @error('password')<span class="error-message">{{ $message }}</span>@enderror
            <div id="password-error" class="error-message hidden"></div>
            <div class="password-strength-container">
                <div class="form-helper">Password strength:</div>
                <div class="strength-bar-bg">
                    <div class="strength-bar" id="strength-bar" style="width: 0%; background: #ef4444;"></div>
                </div>
                <div class="strength-text" id="strength-text">Enter a password</div>
            </div>

            <label class="form-label" style="margin-top: 18px;">Confirm Password <span>*</span></label>
            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation')<span class="error-message">{{ $message }}</span>@enderror
            <div id="password_confirmation-error" class="error-message hidden"></div>

            <div class="button-group">
                <button type="button" onclick="nextStep(3)" class="tf-button btn-back">
                    ← Back
                </button>
                <button type="button" onclick="validateAndNext(4)" class="tf-button style-1">
                    Next →
                </button>
            </div>
        </div>

        <!-- STEP 5: Final -->
        <div class="step hidden" id="step5">
            <div class="alert alert-success">
                <h3>Registration Summary</h3>
                <div class="summary-item">
                    <strong>Email:</strong>
                    <span id="summary-email"></span>
                </div>
                <div class="summary-item">
                    <strong>Company:</strong>
                    <span id="summary-company"></span>
                </div>
                <div class="summary-item">
                    <strong>Phone:</strong>
                    <span id="summary-phone"></span>
                </div>
                <div class="summary-item">
                    <strong>Address:</strong>
                    <span id="summary-address"></span>
                </div>
                <div class="summary-item">
                    <strong>City:</strong>
                    <span id="summary-city"></span>
                </div>
                <div class="summary-item">
                    <strong>Country:</strong>
                    <span id="summary-country"></span>
                </div>
            </div>

            <div class="alert alert-info">
                <h3>Almost there!</h3>
                <p style="margin: 0; font-size: 14px;">Click "Create Account" to complete your registration and access the admin dashboard.</p>
            </div>

            <div class="button-group">
                <button type="button" onclick="nextStep(4)" class="tf-button btn-back">
                    ← Back
                </button>
                <button type="submit" class="tf-button style-1">
                     Create Account
                </button>
            </div>
        </div>
    </form>

    <div class="login-link">
        Already have an account?
        <a href="{{ route('login') }}">Login here</a>
    </div>

    <div class="form-footer">
        © 2026 Softifyx — Simplify • Fix • Elevate
    </div>
</div>

<!-- Styles for Stepper -->
<style>
.wg-box {
            background: white;
            max-width: 520px;
            font-family: 'Jost', sans-serif;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 40px;
    }
    header {
            text-align: center;
            margin-bottom: 36px;
    }

    header h2 {
            font-size: 26px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 8px;
    }

    header p {
            font-size: 14px;
            color: #6b7280;
    }
    /* Stepper Container */
    .stepper-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            position: relative;
    }
    .step-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .indicator {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            color: #9ca3af;
            transition: all 0.3s;
        }

        .step-tab.active .indicator {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .step-tab.completed .indicator {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        .step-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
            text-align: center;
        }

        .step-item.active .step-label {
            color: #2563eb;
        }

    .step-tab.completed .indicator {
        background: #22c55e;
        color: white;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    .step-tab .label {
        font-size: 12px;
        font-weight: 600;
        color: #999;
        text-align: center;
        transition: all 0.3s ease;
        max-width: 60px;
    }

    .step-tab.active .label {
        color: #667eea;
        font-weight: 700;
    }

    .step-tab.completed .label {
        color: #22c55e;
    }

    /* Progress Bar */
    .progress-container {
        margin-bottom: 30px;
    }

    .progress-bar-bg {
        height: 6px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    }

    .progress-bar {
        height: 100%;
        background-color: #22c55e;
        border-radius: 10px;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
    }

    /* Form Steps */
    .step {
        display: block;
        animation: fadeIn 0.4s ease-in;
    }

    .step.hidden {
        display: none !important;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form Labels */
    .form-label {
        display: block;
        font-size: 15px;
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 8px;
        transition: color 0.3s ease;
    }

    .form-label span {
        color: #ef4444;
        margin-left: 4px;
    }

    /* Form Controls */
    .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            background: white;
            color: #1f2937;
    }

    .form-control:focus {
        outline: none;
        border-color: #ff0000;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        background: #f9f9ff;
    }

    .form-control:hover:not(:focus) {
        border-color: #d1d5db;
    }

    .form-control.input-valid {
        border-color: #22c55e !important;
        background: #f0fdf4;
    }

    .form-control.input-error {
        border-color: #ef4444 !important;
        background: #fef2f2;
    }

    /* Form Spacing */
    .form-group {
        margin-bottom: 18px;
    }

    .form-helper {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
        font-weight: 500;
    }

    /* Error Messages */
    .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .error-message::before {
        content: '⚠';
        font-size: 14px;
    }

    /* Password Strength */
    .password-strength-container {
        margin-top: 10px;
    }

    .strength-bar-bg {
        height: 5px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .strength-bar {
        height: 100%;
        border-radius: 10px;
        transition: all 0.4s ease;
    }

    .strength-text {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
    }

    /* Button Styling */
    .button-group {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 28px;
    }

    .tf-button {
        flex: 1;
        padding: 14px 24px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-back {
        background: #f3f4f6;
        color: #6b7280;
        border: 2px solid #e5e7eb;
    }

    .btn-back:hover {
        background: #e5e7eb;
        border-color: #d1d5db;
        transform: translateX(-2px);
    }

    .btn-back:active {
        transform: translateX(-1px);
    }

    .tf-button.style-1 {
        background-color: #2563eb;
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .tf-button.style-1:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .tf-button.style-1:active {
        transform: translateY(0);
    }

    .tf-button.style-1:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Alert Styling */
    .alert {
        padding: 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-success {
        background: #f0fdf4;
        border-left-color: #22c55e;
        color: #15803d;
    }

    .alert-info {
        background: #f0f9ff;
        border-left-color: #0ea5e9;
        color: #0369a1;
    }

    .alert h3 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-success h3::before {
        content: '✓';
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: #22c55e;
        color: white;
        border-radius: 50%;
        font-size: 14px;
    }

    .alert-info h3::before {
        content: 'ℹ';
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: #0ea5e9;
        color: white;
        border-radius: 50%;
        font-size: 16px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-size: 14px;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item strong {
        color: #1f2937;
        font-weight: 600;
    }

    .summary-item span {
        color: #667eea;
        font-weight: 500;
    }

    /* Login Link */
    .login-link {
        text-align: center;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        font-size: 13px;
        color: #6b7280;
    }

    .login-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .login-link a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Footer */
    .form-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
        font-size: 12px;
        color: #9ca3af;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .step-tab .label {
            font-size: 10px;
            max-width: 45px;
        }

        .step-tab .indicator {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        .button-group {
            flex-direction: column-reverse;
        }

        .tf-button {
            width: 100%;
        }
    }

    .hidden {
        display: none !important;
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Enhanced JavaScript -->
<script>
    let currentStep = 1;
    const totalSteps = 5;

    function nextStep(step) {
        const steps = [1, 2, 3, 4, 5];

        // Update progress bar
        const progressBar = document.querySelector('.progress-bar');
        progressBar.style.width = `${(step / totalSteps) * 100}%`;

        steps.forEach(s => {
            document.getElementById(`step${s}`).classList.add('hidden');
            const tab = document.getElementById(`step${s}-tab`);
            const indicator = tab.querySelector('.indicator');

            tab.classList.remove('active', 'completed');
            indicator.innerText = s;
        });

        document.getElementById(`step${step}`).classList.remove('hidden');
        const currentTab = document.getElementById(`step${step}-tab`);
        currentTab.classList.add('active');
        currentTab.classList.remove('completed');

        // Mark previous steps as completed
        for (let i = 1; i < step; i++) {
            const tab = document.getElementById(`step${i}-tab`);
            const indicator = tab.querySelector('.indicator');
            tab.classList.add('completed');
            tab.classList.remove('active');
            indicator.innerText = '✓';
        }

        if (step === 5) {
            updateSummary();
        }

        currentStep = step;
    }

    function validateStep(step) {
        let isValid = true;

        document.querySelectorAll('.text-danger').forEach(el => {
            if (el.id && el.id.includes('-error')) el.classList.add('hidden');
        });
        document.querySelectorAll('input, select').forEach(input => {
            input.classList.remove('input-error', 'input-valid');
        });

        switch(step) {
            case 1:
                const email = document.getElementById('email');
                const emailValue = email.value.trim();

                if (!emailValue) {
                    showError('email', 'Email is required');
                    isValid = false;
                } else if (!isValidEmail(emailValue)) {
                    showError('email', 'Please enter a valid email address');
                    isValid = false;
                } else {
                    email.classList.add('input-valid');
                }
                break;

            case 2:
                const fields = ['company_name', 'phone'];
                const fieldLabels = ['Company Name', 'Business Phone'];

                fields.forEach((field, index) => {
                    const input = document.getElementById(field);
                    const value = input.value.trim();

                    if (!value) {
                        showError(field, `${fieldLabels[index]} is required`);
                        isValid = false;
                    } else if (field === 'phone' && !isValidPhone(value)) {
                        showError(field, 'Please enter a valid phone number');
                        isValid = false;
                    } else {
                        input.classList.add('input-valid');
                    }
                });
                break;

            case 3:
                const locationFields = ['address', 'country'];
                const locationLabels = ['Business Address', 'Country'];

                locationFields.forEach((field, index) => {
                    const input = document.getElementById(field);
                    const value = input.value.trim();

                    if (!value) {
                        showError(field, `${locationLabels[index]} is required`);
                        isValid = false;
                    } else {
                        input.classList.add('input-valid');
                    }
                });
                break;

            case 4:
                const password = document.getElementById('password');
                const passwordConfirmation = document.getElementById('password_confirmation');
                const passwordValue = password.value;
                const confirmValue = passwordConfirmation.value;

                if (!passwordValue) {
                    showError('password', 'Password is required');
                    isValid = false;
                } else if (passwordValue.length < 6) {
                    showError('password', 'Password must be at least 6 characters long');
                    isValid = false;
                } else {
                    password.classList.add('input-valid');
                }

                if (!confirmValue) {
                    showError('password_confirmation', 'Password confirmation is required');
                    isValid = false;
                } else if (passwordValue !== confirmValue) {
                    showError('password_confirmation', 'Passwords do not match');
                    isValid = false;
                } else if (passwordValue === confirmValue && passwordValue.length >= 6) {
                    passwordConfirmation.classList.add('input-valid');
                }
                break;
        }

        return isValid;
    }

    function validateAndNext(currentStep) {
        if (validateStep(currentStep)) {
            nextStep(currentStep + 1);
        }
    }

    function showError(fieldId, message) {
        const errorElement = document.getElementById(`${fieldId}-error`);
        const inputElement = document.getElementById(fieldId);

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
        inputElement.classList.add('input-error');
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[\d\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }

    function updatePasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        let strength = 0;
        let text = 'Too Short';
        let color = 'bg-red-500';

        if (password.length >= 6) strength += 1;
        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/\d/.test(password)) strength += 1;
        if (/[@$!%*?&]/.test(password)) strength += 1;

        switch(strength) {
            case 0:
                text = 'Too Short';
                color = 'bg-red-500';
                break;
            case 1:
                text = 'Weak';
                color = 'bg-orange-500';
                break;
            case 2:
                text = 'Fair';
                color = 'bg-yellow-500';
                break;
            case 3:
                text = 'Good';
                color = 'bg-blue-500';
                break;
            case 4:
            case 5:
            case 6:
                text = 'Strong';
                color = 'bg-green-500';
                break;
        }

        strengthBar.className = `h-2 rounded-full transition-all duration-300 ${color}`;
        strengthBar.style.width = `${Math.min((strength / 4) * 100, 100)}%`;
        strengthText.textContent = text;
    }

    function updateSummary() {
        document.getElementById('summary-email').textContent = document.getElementById('email').value;
        document.getElementById('summary-company').textContent = document.getElementById('company_name').value;
        document.getElementById('summary-phone').textContent = document.getElementById('phone').value;
        document.getElementById('summary-address').textContent = document.getElementById('address').value;
        document.getElementById('summary-city').textContent = document.getElementById('city').value;

        const countrySelect = document.getElementById('country');
        document.getElementById('summary-country').textContent = countrySelect.options[countrySelect.selectedIndex].text;
    }

    function goBack() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/';
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        nextStep(1);

        // Password strength checker
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', updatePasswordStrength);
        }
    });
</script>
@endsection
