<form action="{{ adminRoute('admin.settings.update.appearance') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Colors Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-palette"></i> Brand Colors
        </h3>

        <!-- Primary Color -->
        <div class="mb-20">
            <label class="text-body-text">
                <i class="fas fa-star"></i>Primary Color
            </label>
            <div class="flex items-center gap-4">
                <input
                    type="color"
                    name="primary_color"
                    value="{{ $appearanceSetting->primary_color ?? '#4F46E5' }}"
                    class="input-color">
                <input
                    type="text"
                    name="primary_color_text"
                    value="{{ $appearanceSetting->primary_color ?? '#4F46E5' }}"
                    class="form-control flex-1">
            </div>
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Used for primary buttons, links, and UI elements</p>
            @error('primary_color')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>

        <!-- Secondary Color -->
        <div>
            <label class="text-body-text">
                <i class="fas fa-circle"></i>Secondary Color
            </label>
            <div class="flex items-center gap-4">
                <input
                    type="color"
                    name="secondary_color"
                    value="{{ $appearanceSetting->secondary_color ?? '#06B6D4' }}"
                    class="input-color">
                <input
                    type="text"
                    name="secondary_color_text"
                    value="{{ $appearanceSetting->secondary_color ?? '#06B6D4' }}"
                    class="form-control flex-1">
            </div>
            <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Used for accents and secondary elements</p>
            @error('secondary_color')
                <p class="text-danger mt-8">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Theme Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-moon"></i> Theme Settings
        </h3>

        <label class="text-body-text">
            <i class="fas fa-adjust"></i>Theme Mode
        </label>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex items-center p-10 border border-line rounded-lg cursor-pointer hover:border-primary transition-all" :class="{ 'border-primary bg-primary-light': theme === 'light' }">
                <input
                    type="radio"
                    name="theme"
                    value="light"
                    {{ ($appearanceSetting->theme ?? 'light') === 'light' ? 'checked' : '' }}
                    class="h-4 w-4"
                    x-model="theme">
                <span class="ml-3">
                    <i class="fas fa-sun"></i>
                    <strong>Light Mode</strong>
                </span>
            </label>
            <label class="flex items-center p-10 border border-line rounded-lg cursor-pointer hover:border-primary transition-all" :class="{ 'border-primary bg-primary-light': theme === 'dark' }">
                <input
                    type="radio"
                    name="theme"
                    value="dark"
                    {{ ($appearanceSetting->theme ?? 'light') === 'dark' ? 'checked' : '' }}
                    class="h-4 w-4"
                    x-model="theme">
                <span class="ml-3">
                    <i class="fas fa-moon"></i>
                    <strong>Dark Mode</strong>
                </span>
            </label>
        </div>
        @error('theme')
            <p class="text-danger mt-8">{{ $message }}</p>
        @enderror
    </div>

    <!-- Invoice Settings Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-file-invoice"></i> Invoice Settings
        </h3>

        <label class="text-body-text">
            <i class="fas fa-file-pdf"></i>Invoice Template
        </label>
        <select
            name="invoice_template"
            class="form-control"
            required>
            @foreach(\App\Models\AppearanceSetting::getInvoiceTemplates() as $value => $label)
                <option value="{{ $value }}" {{ ($appearanceSetting->invoice_template ?? 'default') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Choose how your invoices will appear</p>
        @error('invoice_template')
            <p class="text-danger mt-8">{{ $message }}</p>
        @enderror
    </div>

    <!-- Favicon Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-image"></i> Favicon
        </h3>

        <label class="text-body-text">
            <i class="fas fa-image"></i>Browser Tab Icon
        </label>
        <div class="flex items-center gap-6">
            @if($appearanceSetting->favicon)
                <div class="flex-shrink-0">
                    <img src="{{ $appearanceSetting->favicon_url }}" alt="Favicon" class="preview-image">
                </div>
            @endif
            <input
                type="file"
                name="favicon"
                accept="image/x-icon,image/png,image/jpeg"
                class="form-control flex-1">
        </div>
        <p class="text-body-text-2 mt-8"><i class="fas fa-info-circle"></i>Formats: ICO, PNG, JPG (Max 512KB)</p>
        @error('favicon')
            <p class="text-danger mt-8">{{ $message }}</p>
        @enderror
    </div>

    <!-- Color Preview Section -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-eye"></i> Color Preview
        </h3>
        <div class="flex flex-wrap gap-20">
            <div class="text-center">
                <p class="text-body-text mb-8">Primary Color</p>
                <div
                    class="preview-color"
                    style="background-color: var(--primary-color, #4F46E5); transition: background-color 0.3s ease;">
                </div>
            </div>
            <div class="text-center">
                <p class="text-body-text mb-8">Secondary Color</p>
                <div
                    class="preview-color"
                    style="background-color: var(--secondary-color, #06B6D4); transition: background-color 0.3s ease;">
                </div>
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
            <i class="fas fa-save"></i> Save Changes
        </button>
    </div>
</form>
