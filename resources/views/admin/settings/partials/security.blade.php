<form action="{{ adminRoute('admin.settings.update.business') }}" method="POST">
    @csrf

    <!-- Section: Session Management -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-clock"></i> Session Management
        </h3>

        <!-- Session Timeout -->
        <div>
            <label class="text-body-text">
                <i class="fas fa-hourglass-end"></i>Session Timeout (Minutes)
            </label>
            <div class="flex items-center gap-4">
                <input
                    type="number"
                    name="session_timeout_minutes"
                    min="5"
                    max="1440"
                    value="{{ $businessSetting->session_timeout_minutes ?? old('session_timeout_minutes', 30) }}"
                    class="form-control"
                    placeholder="30">
                <span class="text-body-text-2 font-medium whitespace-nowrap">minutes</span>
            </div>
            <p class="text-body-text-2 mt-8">Users will be automatically logged out after this period of inactivity (5-1440 minutes)</p>
            @error('session_timeout_minutes')
                <p class="text-danger mt-8"><i class="fas fa-times-circle"></i>{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Section: Authentication -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-lock"></i> Authentication & Security
        </h3>

        <!-- Two-Factor Authentication -->
        <div class="flex items-center justify-between p-10 border border-line rounded-lg">
            <div class="flex items-center gap-8">
                <i class="fas fa-shield-alt text-2xl"></i>
                <div>
                    <p class="font-semibold text-heading">Two-Factor Authentication (2FA)</p>
                    <p class="text-body-text-2 mt-4">Require users to verify with a second authentication method</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input
                    type="hidden"
                    name="two_factor_enabled"
                    value="0">
                <input
                    type="checkbox"
                    name="two_factor_enabled"
                    value="1"
                    {{ ($businessSetting->two_factor_enabled ?? old('two_factor_enabled')) ? 'checked' : '' }}
                    class="sr-only peer">
                <div class="w-14 h-8 bg-gray-300 peer-checked:bg-primary rounded-full peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:shadow-lg peer-checked:shadow-primary/50"></div>
            </label>
        </div>
        @error('two_factor_enabled')
            <p class="text-danger mt-8"><i class="fas fa-times-circle"></i>{{ $message }}</p>
        @enderror
    </div>

    <!-- Section: Security Information -->
    <div class="alert alert-success">
        <h3 class="text-body-text mb-8 font-semibold">
            <i class="fas fa-info-circle"></i> Security Best Practices
        </h3>
        <ul class="space-y-8 text-body-text-2">
            <li class="flex items-start gap-8">
                <i class="fas fa-check-circle mt-4 flex-shrink-0"></i>
                <span>Enable 2FA to protect user accounts from unauthorized access</span>
            </li>
            <li class="flex items-start gap-8">
                <i class="fas fa-check-circle mt-4 flex-shrink-0"></i>
                <span>Set appropriate session timeouts for public terminals</span>
            </li>
            <li class="flex items-start gap-8">
                <i class="fas fa-check-circle mt-4 flex-shrink-0"></i>
                <span>Regularly review login activity and suspicious access attempts</span>
            </li>
            <li class="flex items-start gap-8">
                <i class="fas fa-check-circle mt-4 flex-shrink-0"></i>
                <span>Keep all passwords strong and stored securely</span>
            </li>
        </ul>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end gap-3">
        <button
            type="reset"
            class="tf-button style-1">
            <i class="fas fa-redo"></i> Reset
        </button>
        <button
            type="submit"
            class="tf-button style-1">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>
