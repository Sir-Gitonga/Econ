<form
    action="{{ adminRoute('admin.settings.update.general') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf

    <!-- Main Wrapper -->
    <div class="wg-box">

        <!-- ================= BASIC INFORMATION ================= -->
        <div class="wg-box">

            <h3 class="wg-title">
                <i class="fas fa-info-circle"></i>
                Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Company Name -->
                <div>
                    <label class="text-body-text">
                        Company Name
                    </label>

                    <input
                        type="text"
                        name="company_name"
                        value="{{ $companySetting->company_name ?? old('company_name') }}"
                        class="form-control"
                        placeholder="Enter company name"
                        required>

                    @error('company_name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Logo -->
                <div>
                    <label class="text-body-text">
                        Company Logo
                    </label>

                    <div
                        class="upload-image upfile"
                    >

                        @if($companySetting->logo)
                            <img
                                src="{{ $companySetting->logo_url }}"
                                class="preview-image"
                            >
                        @else
                            <div class="preview-image">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif

                        <div class="upload-input">
                            <input
                                type="file"
                                name="logo"
                                id="logo-input"
                                accept="image/*"
                                class="hidden">

                            <label for="logo-input">
                                <p class="link">
                                    Upload Logo
                                </p>
                                <p class="text-body-text-2">
                                    PNG, JPG (Max 2MB)
                                </p>
                            </label>
                        </div>

                    </div>

                    @error('logo')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>


        <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>


        <!-- ================= CONTACT INFORMATION ================= -->
        <div class="wg-box">

            <h3 class="wg-title">
                <i class="fas fa-phone"></i>
                Contact Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Email -->
                <div>
                    <label class="text-body-text">
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ $companySetting->email ?? old('email') }}"
                        class="form-control"
                        placeholder="contact@company.com"
                        required>

                    @error('email')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="text-body-text">
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        value="{{ $companySetting->phone ?? old('phone') }}"
                        class="form-control"
                        placeholder="+254712345678">

                    @error('phone')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="text-body-text">
                        WhatsApp Number
                    </label>

                    <input
                        type="tel"
                        name="whatsapp"
                        value="{{ $companySetting->whatsapp ?? old('whatsapp') }}"
                        class="form-control"
                        placeholder="+254712345678">

                    @error('whatsapp')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="text-body-text">
                        Business Address
                    </label>

                    <textarea
                        name="address"
                        rows="3"
                        class="form-control"
                        placeholder="Business address">{{ $companySetting->address ?? old('address') }}</textarea>

                    @error('address')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>


        <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>


        <!-- ================= PREFERENCES ================= -->
        <div class="wg-box">

            <h3 class="wg-title">
                <i class="fas fa-cog"></i>
                Preferences
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Timezone -->
                <div>
                    <label class="text-body-text">
                        Timezone
                    </label>

                    <select
                        name="timezone"
                        class="form-control"
                        required>

                        <option value="">Select Timezone</option>

                        @foreach(timezone_identifiers_list() as $tz)
                            <option
                                value="{{ $tz }}"
                                {{ ($companySetting->timezone ?? old('timezone')) == $tz ? 'selected' : '' }}>
                                {{ $tz }}
                            </option>
                        @endforeach

                    </select>

                    @error('timezone')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency -->
                <div>
                    <label class="text-body-text">
                        Currency
                    </label>

                    <select
                        name="currency"
                        class="form-control"
                        required>

                        <option value="">Select Currency</option>

                        @foreach(['KES'=>'KES','USD'=>'USD','EUR'=>'EUR','GBP'=>'GBP'] as $code => $label)

                            <option
                                value="{{ $code }}"
                                {{ ($companySetting->currency ?? old('currency')) == $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                    @error('currency')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>


        <!-- ================= SAVE ================= -->
        <div class="flex justify-between gap20 flex-wrap">

            <div>
                <p class="text-body-text">
                    Last updated:
                    {{ $companySetting->updated_at?->diffForHumans() ?? 'Never' }}
                </p>
            </div>

            <div class="flex gap-4">

                <button
                    type="reset"
                    class="tf-button style-1">
                    <i class="fas fa-redo"></i>
                    Reset
                </button>

                <button
                    type="submit"
                    class="tf-button style-1">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>

            </div>

        </div>

    </div>

</form>
