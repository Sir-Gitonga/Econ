# Settings Module - Tailwind Styling Guide

## ✅ General Settings - COMPLETE

The **General Settings** form has been beautifully redesigned with Tailwind CSS.

### Features:
- ✅ Organized into 3 sections (Basic Info, Contact, Preferences)
- ✅ Font Awesome icons for each field
- ✅ Enhanced input styling with focus states
- ✅ Drag-and-drop logo upload area
- ✅ Error messages with icons
- ✅ Gradient submit button with hover effects
- ✅ Reset button functionality

---

## 🎯 Styling Pattern Used

All form sections follow this pattern:

### Section Container
```blade
<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-[icon] text-indigo-600"></i> Section Title
    </h3>
    
    <!-- Fields go here -->
</div>
```

### Form Field Pattern
```blade
<div class="mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        <i class="fas fa-[icon] text-indigo-500 mr-2"></i>Field Label
    </label>
    <input
        type="text"
        name="field"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
        placeholder="Placeholder text">
    @error('field')
        <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
            <i class="fas fa-times-circle"></i>{{ $message }}
        </p>
    @enderror
</div>
```

### Button Pattern
```blade
<button
    type="submit"
    class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 shadow-md hover:shadow-lg transition-all font-semibold flex items-center gap-2">
    <i class="fas fa-save"></i> Save Changes
</button>
```

---

## 📋 Remaining Forms to Style

### 1. **Appearance Settings** (appearance.blade.php)

**Sections to create:**
- **Colors Section**
  - Primary Color (with color picker)
  - Secondary Color (with color picker)
  - Live preview boxes

- **Theme Section**
  - Light/Dark toggle radio buttons
  - Theme preview

- **Invoice Section**
  - Invoice template dropdown
  - Preview link

- **Favicon Section**
  - Favicon upload
  - Current favicon preview

**Icons to use:**
- `fas fa-palette` - Colors section
- `fas fa-moon` / `fas fa-sun` - Theme toggle
- `fas fa-file-invoice` - Invoice template
- `fas fa-star` - Favicon

---

### 2. **Payment Settings** (payments.blade.php)

**Sections to create:**
- **Gateway Selection**
  - Radio buttons: M-PESA Only, IntaSend Only, Both
  - Visual cards for each option

- **M-PESA Configuration** (conditional)
  - Paybill/Shortcode input
  - Consumer Key input
  - Consumer Secret (password field with eye toggle)
  - Passkey (password field with eye toggle)
  - Environment (Sandbox/Live select)

- **IntaSend Configuration** (conditional)
  - Publishable Key input
  - Secret Key (password field)
  - Mode (Test/Live select)

- **Testing Section**
  - Warning alert about testing
  - Links to documentation

**Icons to use:**
- `fas fa-credit-card` - Gateway selection
- `fas fa-lock` - Credentials/passwords
- `fas fa-flask` - Testing section

---

### 3. **Business Settings** (business.blade.php)

**Sections to create:**
- **About Company**
  - About Description (textarea)
  - Mission Statement (textarea)
  - Vision Statement (textarea)
  - Services (textarea)

- **Invoicing**
  - Invoice Prefix (text input)
  - Tax/VAT Rate (number 0-100%)
  - VAT PIN (text input)

- **Security**
  - Session Timeout (number in minutes)
  - Two-Factor Authentication (toggle switch)

**Icons to use:**
- `fas fa-building` - Company info
- `fas fa-receipt` - Invoicing
- `fas fa-shield-alt` - Security

---

### 4. **Communication Settings** (communication.blade.php)

**Sections to create:**
- **SMTP Configuration**
  - SMTP Host (text input)
  - SMTP Port (number input)
  - SMTP Username (text input)
  - SMTP Password (password field)
  - From Address (email input)
  - From Name (text input)
  - Encryption (TLS/SSL select)
  - Test Email Button

- **SMS Configuration**
  - SMS Provider (Twilio/Vonage/Africa's Talking select)
  - API Key (password field)

- **Notification Preferences**
  - Email Notifications (checkbox/toggle)
  - SMS Notifications (checkbox/toggle)

- **Help/Examples**
  - Collapsible section with examples for Gmail, SendGrid, etc.

**Icons to use:**
- `fas fa-envelope` - Email/SMTP
- `fas fa-sms` - SMS
- `fas fa-bell` - Notifications
- `fas fa-lightbulb` - Help section

---

## 🎨 Tailwind Classes Reference

### Input Fields
```blade
<!-- Text/Email/Tel/Password -->
class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"

<!-- Textarea -->
class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"

<!-- Select -->
class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
```

### Labels
```blade
<label class="block text-sm font-semibold text-gray-700 mb-2">
    <i class="fas fa-[icon] text-indigo-500 mr-2"></i>Label Text
</label>
```

### Error Messages
```blade
@error('field')
    <p class="text-red-600 text-sm mt-2 flex items-center gap-1">
        <i class="fas fa-times-circle"></i>{{ $message }}
    </p>
@enderror
```

### Buttons
```blade
<!-- Primary Button -->
class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 shadow-md hover:shadow-lg transition-all font-semibold flex items-center gap-2"

<!-- Secondary Button -->
class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-semibold flex items-center gap-2"

<!-- Danger Button -->
class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold flex items-center gap-2"
```

### Toggle Switch
```blade
<label class="flex items-center gap-3 cursor-pointer">
    <div class="relative">
        <input type="checkbox" name="field" class="sr-only peer" {{ condition ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-gray-300 peer-checked:bg-indigo-600 rounded-full peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
    </div>
    <span class="font-semibold text-gray-700">Toggle Label</span>
</label>
```

### Color Picker with Hex Input
```blade
<div class="flex gap-3">
    <input type="color" name="color" value="{{ $value }}" class="w-16 h-10 rounded-lg cursor-pointer">
    <input type="text" name="color_hex" value="{{ $value }}" placeholder="#000000" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
</div>
```

### Section Headers
```blade
<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-[icon] text-indigo-600"></i> Section Title
    </h3>
    <!-- Fields -->
</div>
```

---

## 🚀 Implementation Checklist

- [x] General Settings - Complete with 3 sections
- [ ] Appearance Settings - 4 sections (Colors, Theme, Invoice, Favicon)
- [ ] Payment Settings - 3 sections (Gateway, M-PESA, IntaSend)
- [ ] Business Settings - 3 sections (About, Invoicing, Security)
- [ ] Communication Settings - 4 sections (SMTP, SMS, Notifications, Help)

---

## 💡 Pro Tips

1. **Icons**: Always use Font Awesome `fas fa-*` or `fab fa-*` classes
2. **Colors**: Primary indigo-600, Secondary blue-600, Errors red-600
3. **Spacing**: Use `mb-6` between fields, `p-6` for section padding
4. **Focus States**: All inputs use `focus:ring-2 focus:ring-indigo-500`
5. **Transitions**: Add `transition-all` for smooth animations
6. **Typography**: 
   - Section titles: `text-lg font-semibold text-gray-900`
   - Labels: `text-sm font-semibold text-gray-700`
   - Help text: `text-xs text-gray-500`

---

## 📝 Font Awesome Icons Reference

### Common Settings Icons
- `fas fa-sliders-h` - General settings
- `fas fa-palette` - Colors/Appearance
- `fas fa-credit-card` - Payments
- `fas fa-chart-bar` - Business/Analytics
- `fas fa-envelope` - Email/Communication
- `fas fa-cog` - Settings
- `fas fa-lock` - Security
- `fas fa-eye` / `fas fa-eye-slash` - Show/Hide password
- `fas fa-check` - Success
- `fas fa-times-circle` - Error
- `fas fa-info-circle` - Information
- `fas fa-lightbulb` - Help/Tip
- `fas fa-upload` - Upload
- `fas fa-download` - Download
- `fas fa-save` - Save
- `fas fa-redo` - Reset

---

## 🎯 Next Steps

1. Apply the styling pattern to **appearance.blade.php**
2. Apply to **payments.blade.php** with conditional fields
3. Apply to **business.blade.php** with toggle switches
4. Apply to **communication.blade.php** with collapsible help section
5. Test responsive design on mobile devices
6. Verify all error states display correctly

Each form should feel consistent with the General Settings design while having its own visual identity through the unique icons and section organization.
