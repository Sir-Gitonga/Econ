<form action="{{ adminRoute('admin.settings.update.payment') }}" method="POST" x-data="{ gateway: '{{ $paymentSetting->gateway ?? 'mpesa' }}' }">
    @csrf

    <!-- Payment Gateway Selection Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-credit-card"></i> Select Payment Gateway
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(\App\Models\PaymentSetting::getGateways() as $value => $label)
                <label class="flex items-center p-10 border border-line rounded-lg cursor-pointer hover:border-primary transition-all" :class="{ 'border-primary bg-primary-light': gateway === '{{ $value }}' }">
                    <input
                        type="radio"
                        name="gateway"
                        value="{{ $value }}"
                        @change="gateway = '{{ $value }}'"
                        {{ ($paymentSetting->gateway ?? 'mpesa') === $value ? 'checked' : '' }}
                        class="h-4 w-4">
                    <span class="ml-3">
                        <i class="fas fa-circle-check"></i>
                        <span class="font-medium">{{ $label }}</span>
                    </span>
                </label>
            @endforeach
        </div>
        @error('gateway')
            <p class="text-danger mt-8">{{ $message }}</p>
        @enderror
    </div>

    <!-- M-PESA Configuration Section -->
    <div x-show="gateway === 'mpesa' || gateway === 'both'" class="wg-box transition-all" x-transition>
        <h3 class="wg-title">
            <i class="fas fa-mobile-alt"></i> M-PESA Configuration
        </h3>

        <!-- Paybill and Environment Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-20">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-barcode"></i>Paybill/Shortcode
                </label>
                <input
                    type="text"
                    name="mpesa_paybill"
                    value="{{ $paymentSetting->mpesa_paybill ?? old('mpesa_paybill') }}"
                    placeholder="e.g., 123456"
                    class="form-control">
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your Mpesa paybill or shortcode number</p>
                @error('mpesa_paybill')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-server"></i>Environment
                </label>
                <select
                    name="mpesa_environment"
                    class="form-control">
                    @foreach(\App\Models\PaymentSetting::getMpesaEnvironments() as $value => $label)
                        <option value="{{ $value }}" {{ ($paymentSetting->mpesa_environment ?? 'sandbox') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Use Sandbox for testing, Production for live</p>
                @error('mpesa_environment')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Consumer Key -->
        <div class="mb-20 pb-20 border-b border-line">
            <label class="text-body-text">
                <i class="fas fa-key"></i>Consumer Key
            </label>
            <input
                type="text"
                name="mpesa_consumer_key"
                value="{{ $paymentSetting->mpesa_consumer_key ?? old('mpesa_consumer_key') }}"
                class="form-control">
            @error('mpesa_consumer_key')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>

        <!-- Consumer Secret -->
        <div class="mb-20 pb-20 border-b border-line">
            <label class="text-body-text">
                <i class="fas fa-lock"></i>Consumer Secret (Encrypted)
            </label>
            <input
                type="password"
                name="mpesa_consumer_secret"
                placeholder="Leave blank to keep existing"
                class="form-control">
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your secret will be encrypted and stored securely</p>
            @error('mpesa_consumer_secret')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>

        <!-- Passkey -->
        <div>
            <label class="text-body-text">
                <i class="fas fa-key"></i>Passkey (Encrypted)
            </label>
            <input
                type="password"
                name="mpesa_passkey"
                placeholder="Leave blank to keep existing"
                class="form-control">
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Used for Lipa na M-Pesa online (STK Push)</p>
            @error('mpesa_passkey')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- IntaSend Configuration Section -->
    <div x-show="gateway === 'intasend' || gateway === 'both'" class="wg-box transition-all" x-transition>
        <h3 class="wg-title">
            <i class="fas fa-credit-card"></i> IntaSend Configuration
        </h3>

        <!-- Publishable Key and Mode Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-20">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-key"></i>Publishable Key
                </label>
                <input
                    type="text"
                    name="intasend_publishable_key"
                    value="{{ $paymentSetting->intasend_publishable_key ?? old('intasend_publishable_key') }}"
                    placeholder="pk_..."
                    class="form-control">
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your public key for client-side operations</p>
                @error('intasend_publishable_key')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-toggle-on"></i>Mode
                </label>
                <select
                    name="intasend_mode"
                    class="form-control">
                    @foreach(\App\Models\PaymentSetting::getIntasendModes() as $value => $label)
                        <option value="{{ $value }}" {{ ($paymentSetting->intasend_mode ?? 'test') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Test or Production mode</p>
                @error('intasend_mode')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Secret Key -->
        <div>
            <label class="text-body-text">
                <i class="fas fa-lock"></i>Secret Key (Encrypted)
            </label>
            <input
                type="password"
                name="intasend_secret_key"
                placeholder="Leave blank to keep existing"
                class="form-control">
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your secret will be encrypted and stored securely</p>
            @error('intasend_secret_key')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Help & Tips Section -->
    <div class="alert alert-info">
        <h3 class="text-body-text mb-8">
            <i class="fas fa-lightbulb"></i> Important Tips
        </h3>
        <ul class="space-y-8">
            <li class="flex gap-8">
                <i class="fas fa-check-circle"></i>
                <span>Use <strong>Sandbox</strong> environment for testing payment flows</span>
            </li>
            <li class="flex gap-8">
                <i class="fas fa-check-circle"></i>
                <span>Switch to <strong>Production</strong> only after thorough testing</span>
            </li>
            <li class="flex gap-8">
                <i class="fas fa-check-circle"></i>
                <span>Keep your API keys <strong>secret</strong> and never share them</span>
            </li>
            <li class="flex gap-8">
                <i class="fas fa-check-circle"></i>
                <span>Test the connection after saving your gateway credentials</span>
            </li>
        </ul>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end gap-3">
        <button
            type="reset"
            class="tf-button style-1">
            <i class="fas fa-redo"></i> Reset
        </button>
        <button
            type="submit"
            class="tf-button style-1">
            <i class="fas fa-save"></i> Save Payment Settings
        </button>
    </div>
</form>
