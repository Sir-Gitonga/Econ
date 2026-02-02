# Settings UI - Visual Design Summary

## Page Structure

```
┌─────────────────────────────────────────────────────────────────────┐
│  [⚙️] Company Settings                                              │
│  Manage your business configuration and preferences                 │
│  (Gradient Header: Indigo → Blue → Teal)                           │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  [📋] General | [🎨] Appearance | [💳] Payments | [ℹ️] About        │
│  [✉️] Communication | [🔐] Security | [📊] Business                │
│  (Horizontal Tabs with Icons - Active: Indigo bg + border-b-4)     │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│  [✓ Success Message] or [✗ Error Message]                          │
├─────────────────────────────────────────────────────────────────────┤
│  Form Content with 3-7 Sections                                    │
│  [Section 1]   [Section 2]   [Section 3]                          │
│  Fields...     Fields...     Fields...                            │
│                                                                    │
│  [🔄 Reset]  [💾 Save Changes]                                   │
└─────────────────────────────────────────────────────────────────────┘
```

## Color Palette

### Primary Gradient (Header)
```
Indigo-600 (Start)
    ↓
Blue-500 (Middle)
    ↓
Teal-400 (End)
```

### Button Gradient (Action)
```
Indigo-600 (Start)
    ↓
Blue-600 (End)
    ↓
Hover: Indigo-700 → Blue-700
```

### Status Colors
```
✓ Success  → Green-50 bg, Green-500 icon
✗ Error    → Red-50 bg, Red-500 icon
ℹ️ Info     → Blue-50 bg, Blue-500 icon
⚠️ Warning → Amber-50 bg, Amber-500 icon
```

## Form Field Pattern

```
┌─────────────────────────────────────────┐
│  [Icon] Field Label                     │
│  ┌─────────────────────────────────────┐│
│  │ Input/Select/Textarea               ││
│  │ Focus: Ring-2 Indigo-500            ││
│  └─────────────────────────────────────┘│
│  ℹ️ Help text in gray                  │
│  ✗ Error message in red (if any)       │
└─────────────────────────────────────────┘
```

## Specific Form Layouts

### 1. GENERAL SETTINGS
```
┌─ Basic Information ─────────────────┐
│ Company Name      │ Logo Upload     │
│ Email             │                 │
│ Phone     │ WhatsApp                │
└─────────────────────────────────────┘

┌─ Contact Information ───────────────┐
│ Address (Full Width)                │
└─────────────────────────────────────┘

┌─ Preferences ───────────────────────┐
│ Timezone          │ Currency        │
└─────────────────────────────────────┘
```

### 2. APPEARANCE SETTINGS
```
┌─ Brand Colors ──────────────────────┐
│ Primary Color   [Color Picker] [#]  │
│ Secondary Color [Color Picker] [#]  │
└─────────────────────────────────────┘

┌─ Theme Settings ────────────────────┐
│ ◉ Light Mode  │ ○ Dark Mode        │
└─────────────────────────────────────┘

┌─ Invoice Settings ──────────────────┐
│ Invoice Template [Dropdown]         │
└─────────────────────────────────────┘

┌─ Favicon ───────────────────────────┐
│ [Preview] File Upload               │
└─────────────────────────────────────┘

┌─ Color Preview ─────────────────────┐
│ Primary: [■■■]  Secondary: [■■■]   │
└─────────────────────────────────────┘
```

### 3. PAYMENT SETTINGS
```
┌─ Gateway Selection ─────────────────┐
│ ◉ M-PESA  │ ○ IntaSend │ ○ Both   │
└─────────────────────────────────────┘

┌─ M-PESA Configuration (Conditional) ┐
│ Paybill      │ Environment          │
│ Consumer Key (Full Width)           │
│ Consumer Secret                     │
│ Passkey                             │
└─────────────────────────────────────┘

┌─ IntaSend Configuration (Conditional) ┐
│ Publishable Key  │ Mode             │
│ Secret Key (Full Width)             │
└─────────────────────────────────────┘

┌─ Tips ──────────────────────────────┐
│ ✓ Use Sandbox for testing           │
│ ✓ Switch to Production when ready   │
│ ✓ Keep API keys secret              │
└─────────────────────────────────────┘
```

### 4. ABOUT SETTINGS
```
┌─ Company Information ───────────────┐
│ About Description (Full Width)      │
│ Mission Statement    │ Vision       │
│ Services Offered (Full Width)       │
└─────────────────────────────────────┘
```

### 5. COMMUNICATION SETTINGS
```
┌─ SMTP Configuration ────────────────┐
│ SMTP Host       │ SMTP Port         │
│ Username        │ Encryption        │
│ Password (Full Width)               │
│ From Address    │ From Name         │
└─────────────────────────────────────┘

┌─ SMS Configuration ─────────────────┐
│ Provider           │ API Key        │
└─────────────────────────────────────┘

┌─ Notification Preferences ──────────┐
│ ☑ Email Notifications               │
│   Description...                    │
│                                     │
│ ☑ SMS Notifications                 │
│   Description...                    │
└─────────────────────────────────────┘

┌─ Provider Guide ────────────────────┐
│ Gmail   │ SendGrid  │ African SMS    │
│ Details │ Details   │ Details        │
└─────────────────────────────────────┘
```

### 6. SECURITY SETTINGS
```
┌─ Session Management ────────────────┐
│ Session Timeout (minutes) [Input]   │
│ Help text...                        │
└─────────────────────────────────────┘

┌─ Authentication ────────────────────┐
│ Two-Factor Authentication    [Toggle]│
│ Help text...                        │
└─────────────────────────────────────┘

┌─ Security Best Practices ───────────┐
│ ✓ Requirement 1                     │
│ ✓ Requirement 2                     │
│ ✓ Requirement 3                     │
└─────────────────────────────────────┘
```

### 7. BUSINESS SETTINGS
```
┌─ About Your Company ────────────────┐
│ About Description (Full Width)      │
│ Mission Statement    │ Vision       │
│ Services Offered (Full Width)       │
└─────────────────────────────────────┘

┌─ Invoice Settings ──────────────────┐
│ Invoice Prefix  │ Tax Rate  │ VAT PIN│
└─────────────────────────────────────┘

┌─ Security Settings ─────────────────┐
│ Session Timeout (minutes) [Input]   │
│ Two-Factor Authentication    [Toggle]│
└─────────────────────────────────────┘
```

## Tab Navigation States

### Inactive Tab
```
[Icon] Label
gray-600 text, border-b-2 border-gray-200, hover:bg-gray-50
```

### Active Tab
```
[Icon] Label
indigo-600 text, bg-indigo-50, border-b-4 border-indigo-600
```

### Responsive
```
Desktop: [Icon] Label
Mobile:  [Icon] (Label hidden until tap)
```

## Button States

### Primary Save Button
```
Normal:  [💾 Save Changes]
         bg-gradient-to-r from-indigo-600 to-blue-600
         shadow-md

Hover:   [💾 Save Changes]
         bg-gradient-to-r from-indigo-700 to-blue-700
         shadow-lg (elevated)

Active:  [💾 Save Changes]
         Slightly darker gradient
```

### Secondary Reset Button
```
Normal:  [🔄 Reset]
         bg-gray-200 text-gray-800

Hover:   [🔄 Reset]
         bg-gray-300
```

## Alert Messages

### Success
```
┌─ ✓ ─────────────────────────────────────┐
│ Your settings have been saved!          │
│ (animate-pulse effect)                  │
└─────────────────────────────────────────┘
Background: green-50, Border-left: green-500
Icon: Green, Text: Green
```

### Error
```
┌─ ✗ Please fix these errors: ────────────┐
│ • Error message 1                       │
│ • Error message 2                       │
│ • Error message 3                       │
└─────────────────────────────────────────┘
Background: red-50, Border-left: red-500
Icon: Red, Text: Red
```

## Icon Legend

### Navigation
- ⚙️ = fas fa-cog
- 📋 = fas fa-sliders-h
- 🎨 = fas fa-palette
- 💳 = fas fa-credit-card
- ℹ️ = fas fa-info-circle
- ✉️ = fas fa-envelope
- 🔐 = fas fa-shield-alt
- 📊 = fas fa-chart-bar

### Actions
- 💾 = fas fa-save
- 🔄 = fas fa-redo
- ✓ = fas fa-check-circle
- ✗ = fas fa-times-circle
- ℹ️ = fas fa-info-circle

### Form Fields
- [Icon] = Contextual icon (varies by field type)
- Toggle = Custom styled checkbox with peer-checked

## Spacing System

### Padding
- Buttons: `px-6 py-2.5`
- Form sections: `p-6`
- Card content: `p-8`
- Tab nav: `px-6 py-4`

### Margins
- Between sections: `mb-6`
- Between field rows: `mb-6`
- Before buttons: `mb-8`
- Between buttons: `gap-3`

### Gaps
- Within containers: `gap-2` (icons to text)
- Grid columns: `gap-6`
- Tab items: `gap-0` (no space)

## Typography

### Headings
- H1 (Page Title): `text-4xl font-extrabold text-white`
- H2 (Form Title): `text-lg font-semibold text-gray-900`
- H3 (Section): `text-sm font-semibold text-gray-700`

### Body Text
- Labels: `text-sm font-semibold text-gray-700`
- Help Text: `text-gray-600 text-sm mt-2`
- Error Text: `text-red-600 text-sm mt-2`
- Input: `font-mono text-sm` (for codes)

## Transitions & Animations

### Tab Switching
```
Enter: ease-out duration-300
Leave: ease-in duration-200
Effect: Smooth fade + slide
```

### Button Hover
```
Gradient: Immediate smooth color transition
Shadow: From shadow-md to shadow-lg
Transform: Scale 110% on icons (hover)
```

### Input Focus
```
Border: From gray-300 to transparent
Ring: ring-2 ring-indigo-500
Background: Subtle blue tint
Duration: Instant
```

## Responsive Breakpoints

### Mobile (< 768px)
- Single column layouts
- Tab labels hidden (icons only)
- Smaller padding (px-4)
- Full width inputs

### Tablet (768px - 1024px)
- Two column layouts
- Tab labels visible
- Medium padding
- Balanced spacing

### Desktop (> 1024px)
- Three column layouts (where applicable)
- All features visible
- Larger padding (px-6, px-8)
- Generous spacing

## Implementation Quality

✓ Accessibility: Proper labels, contrast ratios, semantic HTML
✓ Performance: No JavaScript animations, CSS transitions only
✓ Compatibility: Works in all modern browsers
✓ Mobile-first: Responsive from 320px width
✓ Consistent: Unified design language throughout
✓ Intuitive: Clear visual hierarchy and flow
✓ Professional: Enterprise-grade appearance
✓ Maintainable: Organized code structure
