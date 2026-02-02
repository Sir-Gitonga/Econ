<form action="{{ adminRoute('admin.settings.update.business') }}" method="POST">
    @csrf

    <!-- About Your Company Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-info-circle"></i> About Your Company
        </h3>

        <!-- About Description -->
        <div class="mb-20 pb-20 border-b border-line">
            <label class="text-body-text">
                <i class="fas fa-pen-fancy"></i>About Description
            </label>
            <textarea
                name="about_description"
                rows="4"
                placeholder="Tell customers about your company..."
                class="form-control">{{ $businessSetting->about_description ?? old('about_description') }}</textarea>
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Brief description of your company's background and values</p>
            @error('about_description')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>

        <!-- Mission and Vision Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-20">
            <div>
                <label class="text-body-text">
                    <i class="fas fa-target"></i>Mission Statement
                </label>
                <textarea
                    name="mission"
                    rows="3"
                    placeholder="What is your mission?"
                    class="form-control">{{ $businessSetting->mission ?? old('mission') }}</textarea>
                @error('mission')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-body-text">
                    <i class="fas fa-eye"></i>Vision Statement
                </label>
                <textarea
                    name="vision"
                    rows="3"
                    placeholder="What is your vision?"
                    class="form-control">{{ $businessSetting->vision ?? old('vision') }}</textarea>
                @error('vision')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Services Offered -->
        <div>
            <label class="text-body-text">
                <i class="fas fa-concierge-bell"></i>Services Offered
            </label>
            <textarea
                name="services"
                rows="4"
                placeholder="List your services..."
                class="form-control">{{ $businessSetting->services ?? old('services') }}</textarea>
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Describe the services and products you offer</p>
            @error('services')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Invoice Settings Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-file-invoice-dollar"></i> Invoice Settings
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Invoice Prefix -->
            <div>
                <label class="text-body-text">
                    <i class="fas fa-hashtag"></i>Invoice Prefix
                </label>
                <input
                    type="text"
                    name="invoice_prefix"
                    value="{{ $businessSetting->invoice_prefix ?? 'INV' }}"
                    class="form-control"
                    placeholder="INV"
                    maxlength="10"
                    required>
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Example: INV-001001</p>
                @error('invoice_prefix')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tax Rate -->
            <div>
                <label class="text-body-text">
                    <i class="fas fa-percent"></i>Tax Rate (%)
                </label>
                <input
                    type="number"
                    name="tax_rate"
                    value="{{ $businessSetting->tax_rate ?? 0 }}"
                    step="0.01"
                    min="0"
                    max="100"
                    class="form-control"
                    required>
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>VAT/Tax percentage (0-100)</p>
                @error('tax_rate')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <!-- VAT/Tax PIN -->
            <div>
                <label class="text-body-text">
                    <i class="fas fa-id-card"></i>VAT/Tax PIN
                </label>
                <input
                    type="text"
                    name="vat_pin"
                    value="{{ $businessSetting->vat_pin ?? old('vat_pin') }}"
                    class="form-control"
                    placeholder="P001234567A">
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Your tax registration number</p>
                @error('vat_pin')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Security Settings Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-shield-alt"></i> Security Settings
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Session Timeout -->
            <div>
                <label class="text-body-text">
                    <i class="fas fa-hourglass-end"></i>Session Timeout (Minutes)
                </label>
                <input
                    type="number"
                    name="session_timeout_minutes"
                    value="{{ $businessSetting->session_timeout_minutes ?? 30 }}"
                    min="5"
                    max="1440"
                    class="form-control"
                    required>
                <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Auto-logout after inactivity (5 min - 24 hours)</p>
                @error('session_timeout_minutes')
                    <p class="text-danger mt-8">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2FA Toggle -->
            <div class="flex items-center justify-between p-10 border border-line rounded-lg hover:border-primary hover:bg-primary-light transition-all">
                <div>
                    <label class="text-body-text flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i>Two-Factor Authentication
                    </label>
                    <p class="text-body-text-2 mt-4"><i class="fas fa-info-circle"></i>Require 2FA for admin login</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="two_factor_enabled"
                        value="1"
                        {{ ($businessSetting->two_factor_enabled ?? false) ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
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
            <i class="fas fa-save"></i> Save Business Settings
        </button>
    </div>
</form>
