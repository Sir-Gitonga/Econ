<form action="{{ adminRoute('admin.settings.update.business') }}" method="POST">
    @csrf

    <!-- Section: Company Description -->
    <div class="wg-box">
        <h3 class="wg-title">
            <i class="fas fa-building"></i> Company Information
        </h3>

        <!-- About Description -->
        <div class="mb-20">
            <label class="text-body-text">
                <i class="fas fa-pen-fancy"></i>About Your Company
            </label>
            <textarea
                name="about_description"
                rows="4"
                class="form-control"
                placeholder="Describe your company, products, and services...">{{ $businessSetting->about_description ?? old('about_description') }}</textarea>
            <p class="text-body-text-2 mt-8">This will be displayed on your public profile</p>
            @error('about_description')
                <p class="text-danger mt-8"><i class="fas fa-times-circle"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Mission Statement -->
        <div class="mb-20">
            <label class="text-body-text">
                <i class="fas fa-target"></i>Mission Statement
            </label>
            <textarea
                name="mission"
                rows="3"
                class="form-control"
                placeholder="What is your company's mission?">{{ $businessSetting->mission ?? old('mission') }}</textarea>
            @error('mission')
                <p class="text-danger mt-8"><i class="fas fa-times-circle"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Vision Statement -->
        <div class="mb-20">
            <label class="text-body-text">
                <i class="fas fa-eye"></i>Vision Statement
            </label>
            <textarea
                name="vision"
                rows="3"
                class="form-control"
                placeholder="What is your company's vision?">{{ $businessSetting->vision ?? old('vision') }}</textarea>
            @error('vision')
                <p class="text-danger mt-8"><i class="fas fa-times-circle"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Services -->
        <div>
            <label class="text-body-text">
                <i class="fas fa-concierge-bell"></i>Services Offered
            </label>
            <textarea
                name="services"
                rows="3"
                class="form-control"
                placeholder="List the services you offer...">{{ $businessSetting->services ?? old('services') }}</textarea>
            @error('services')
                <p class="text-danger mt-8"><i class="fas fa-times-circle"></i>{{ $message }}</p>
            @enderror
        </div>
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
