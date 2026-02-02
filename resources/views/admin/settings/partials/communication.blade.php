<form action="{{ adminRoute('admin.settings.update.communication') }}" method="POST">
    @csrf

    <!-- SMTP Email Configuration Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-envelope"></i> SMTP Email Configuration
        </h3>

        <!-- SMTP Host and Port Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-20">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-server"></i>SMTP Host
                </label>
                <input
                    type="text"
                    name="smtp_host"
                    value="{{ $communicationSetting->smtp_host ?? old('smtp_host') }}"
                    placeholder="smtp.gmail.com"
                    class="form-control">
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your mail server address</p>
                @error('smtp_host')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-plug"></i>SMTP Port
                </label>
                <input
                    type="number"
                    name="smtp_port"
                    value="{{ $communicationSetting->smtp_port ?? 587 }}"
                    min="1"
                    max="65535"
                    class="form-control"
                    placeholder="587">
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Typically 587 (TLS) or 465 (SSL)</p>
                @error('smtp_port')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- SMTP Username and Encryption Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-20 pb-20 border-b border-line">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-user"></i>SMTP Username
                </label>
                <input
                    type="email"
                    name="smtp_username"
                    value="{{ $communicationSetting->smtp_username ?? old('smtp_username') }}"
                    placeholder="your-email@gmail.com"
                    class="form-control">
                @error('smtp_username')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-lock"></i>SMTP Encryption
                </label>
                <select
                    name="smtp_encryption"
                    class="form-control">
                    <option value="">-- Select Encryption --</option>
                    @foreach(\App\Models\CommunicationSetting::getEncryptionOptions() as $value => $label)
                        <option value="{{ $value }}" {{ ($communicationSetting->smtp_encryption ?? 'tls') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('smtp_encryption')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- SMTP Password -->
        <div class="mb-20 pb-20 border-b border-line">
            <label class="text-body-text">
                <i class="fas fa-key"></i>SMTP Password (Encrypted)
            </label>
            <input
                type="password"
                name="smtp_password"
                placeholder="Leave blank to keep existing"
                class="form-control">
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your password will be encrypted and stored securely</p>
            @error('smtp_password')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>

        <!-- From Address and From Name Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-envelope"></i>From Address
                </label>
                <input
                    type="email"
                    name="smtp_from_address"
                    value="{{ $communicationSetting->smtp_from_address ?? old('smtp_from_address') }}"
                    placeholder="noreply@example.com"
                    class="form-control">
                @error('smtp_from_address')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-signature"></i>From Name
                </label>
                <input
                    type="text"
                    name="smtp_from_name"
                    value="{{ $communicationSetting->smtp_from_name ?? old('smtp_from_name') }}"
                    placeholder="Your Company"
                    class="form-control">
                @error('smtp_from_name')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- SMS Configuration Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-sms"></i> SMS Configuration
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-building"></i>SMS Provider
                </label>
                <select
                    name="sms_provider"
                    class="form-control">
                    <option value="">-- Select Provider --</option>
                    @foreach(\App\Models\CommunicationSetting::getSmsProviders() as $value => $label)
                        <option value="{{ $value }}" {{ ($communicationSetting->sms_provider ?? '') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Choose your SMS gateway provider</p>
                @error('sms_provider')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-key"></i>SMS API Key (Encrypted)
                </label>
                <input
                    type="password"
                    name="sms_api_key"
                    placeholder="Leave blank to keep existing"
                    class="form-control">
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your API key will be encrypted</p>
                @error('sms_api_key')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Notification Preferences Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-bell"></i> Notification Preferences
        </h3>

        <div class="space-y-8">
            <!-- Email Notifications -->
            <label class="flex items-start p-10 border border-line rounded-lg cursor-pointer hover:border-primary hover:bg-primary-light transition-all">
                <input
                    type="checkbox"
                    name="email_notifications_enabled"
                    value="1"
                    {{ ($communicationSetting->email_notifications_enabled ?? true) ? 'checked' : '' }}
                    class="h-5 w-5 mt-4 flex-shrink-0">
                <span class="ml-4 flex-1">
                    <strong class="text-heading flex items-center gap-2">
                        <i class="fas fa-envelope"></i>Email Notifications
                    </strong>
                    <p class="text-body-text-2 mt-4">Send order confirmations and account notifications via email</p>
                </span>
            </label>

            <!-- SMS Notifications -->
            <label class="flex items-start p-10 border border-line rounded-lg cursor-pointer hover:border-primary hover:bg-primary-light transition-all">
                <input
                    type="checkbox"
                    name="sms_notifications_enabled"
                    value="1"
                    {{ ($communicationSetting->sms_notifications_enabled ?? true) ? 'checked' : '' }}
                    class="h-5 w-5 mt-4 flex-shrink-0">
                <span class="ml-4 flex-1">
                    <strong class="text-heading flex items-center gap-2">
                        <i class="fas fa-mobile-alt"></i>SMS Notifications
                    </strong>
                    <p class="text-body-text-2 mt-4">Send order updates and alerts via SMS text message</p>
                </span>
            </label>
        </div>
    </div>

    <!-- Help & Examples Section -->
    <div class="alert alert-info">
        <h3 class="text-body-text mb-8">
            <i class="fas fa-lightbulb"></i> Provider Configuration Guide
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <p class="font-semibold mb-8 flex items-center gap-2">
                    <i class="fas fa-envelope"></i>Gmail SMTP
                </p>
                <ul class="text-body-text-2 space-y-4 list-disc list-inside">
                    <li>Host: smtp.gmail.com</li>
                    <li>Port: 587 (TLS) or 465 (SSL)</li>
                    <li>Use App Password, not your regular password</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-8 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>SendGrid SMTP
                </p>
                <ul class="text-body-text-2 space-y-4 list-disc list-inside">
                    <li>Host: smtp.sendgrid.net</li>
                    <li>Port: 587</li>
                    <li>Username: apikey</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-8 flex items-center gap-2">
                    <i class="fas fa-globe"></i>African SMS Providers
                </p>
                <ul class="text-body-text-2 space-y-4 list-disc list-inside">
                    <li>Africa's Talking</li>
                    <li>Twilio</li>
                    <li>Vonage (Nexmo)</li>
                </ul>
            </div>
        </div>
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
            <i class="fas fa-save"></i> Save Communication Settings
        </button>
    </div>
</form>
