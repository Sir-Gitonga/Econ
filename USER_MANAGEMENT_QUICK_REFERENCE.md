# 🚀 TENANT USER MANAGEMENT - QUICK REFERENCE

## ⚡ 5-MINUTE START

### 1. Run Dev Server
```bash
php artisan serve --host=0.0.0.0 --port=8010
```

### 2. Log In at
```
http://nano.localhost:8010/login
Email: test@test.com
Password: password
```

### 3. Go to Users
```
http://nano.localhost:8010/admin/users
```

### 4. Try Actions
- **Create**: Click "Add User" button
- **Edit**: Click "Edit" on any user
- **Toggle**: Click "Disable"/"Enable"
- **Reset**: Click "Reset Password"
- **Delete**: Click "Delete"

---

## 📋 FILES AT A GLANCE

| File | What | Lines |
|------|------|-------|
| `routes/web.php` | Route definitions | 125-130 |
| `app/Http/Controllers/Admin/UserController.php` | All 8 controller methods | 1-237 |
| `app/Http/Requests/Admin/StoreUserRequest.php` | Create validation | 1-38 |
| `app/Http/Requests/Admin/UpdateUserRequest.php` | Edit validation | 1-39 |
| `resources/views/admin/users/index.blade.php` | User list with actions | 1-76 |
| `resources/views/admin/users/create.blade.php` | Create form | 1-65 |
| `resources/views/admin/users/edit.blade.php` | Edit form | 1-65 |

---

## 🔗 ROUTE NAMES TO URLs

| Route Name | HTTP | URL |
|-----------|------|-----|
| `admin.users.index` | GET | `/admin/users` |
| `admin.users.create` | GET | `/admin/users/create` |
| `admin.users.store` | POST | `/admin/users` |
| `admin.users.edit` | GET | `/admin/users/{id}/edit` |
| `admin.users.update` | PUT | `/admin/users/{id}` |
| `admin.users.destroy` | DELETE | `/admin/users/{id}` |
| `admin.users.toggle_status` | POST | `/admin/users/{id}/toggle-status` |
| `admin.users.reset_password` | POST | `/admin/users/{id}/reset-password` |

---

## 🎯 COMMON TASKS

### Add Validation Rule
1. Edit `app/Http/Requests/Admin/StoreUserRequest.php` (create) or `UpdateUserRequest.php` (edit)
2. Modify the `rules()` method
3. Add custom message in `messages()` method

**Example:** Require at least 5 characters in password
```php
'password' => 'required|string|min:8|confirmed|regex:/[A-Z][a-z][0-9]/'
```

### Change Password Reset Message
1. Edit `UserController.php` line 220
2. Modify the message text:
```php
return back()->with('success', "Your custom message here");
```

### Add Email Notification
1. Create a Mailable: `php artisan make:mail UserPasswordReset`
2. Edit `resetPassword()` method:
```php
Mail::send(new UserPasswordReset($user, $temporaryPassword));
return back()->with('success', "Password reset. Email sent to {$user->email}");
```

### Change User List Sort
1. Edit `UserController.php` line 54
2. Change: `User::orderBy('name')`
3. Examples:
   - By email: `User::orderBy('email')`
   - By role: `User::orderBy('role')`
   - By status: `User::orderBy('status', 'desc')`

### Change Pagination Count
1. Edit `UserController.php` line 55
2. Change: `->paginate(25)`
3. Examples:
   - 10 per page: `->paginate(10)`
   - 50 per page: `->paginate(50)`
   - All: `->get()` (no pagination)

---

## 🐛 TROUBLESHOOTING

### Issue: "Validation Error: email is required"
**Cause:** Form field missing or wrong name
**Fix:** Check form input `name="email"` attribute

### Issue: "404 Not Found" on Edit
**Cause 1:** User doesn't exist
**Cure:** User may have been deleted

**Cause 2:** User belongs to different tenant
**Cure:** This is correct behavior - tenant isolation working!

### Issue: "Unauthorized. You do not belong to this company"
**Cause:** User trying to access wrong tenant subdomain
**Fix:** Make sure user is accessing correct tenant URL
- Correct: `nano.localhost:8010`
- Wrong: `other.localhost:8010`

### Issue: "CSRF token mismatch"
**Cause:** Form missing `@csrf` token
**Fix:** Add to form: `@csrf`

### Issue: "Passwords do not match"
**Cause:** Password and password_confirmation fields don't match
**Fix:** Type same password twice

---

## 🔐 TENANT ISOLATION VERIFIED

```
Test Cross-Tenant Access Prevention:

1. Login as shop1.localhost admin
2. Try to access shop2's user:
   http://shop1.localhost:8010/admin/users/999/edit
   
3. If user 999 belongs to shop2:
   ✓ Returns 404 (good - tenant isolation works!)
   
4. Cannot modify/view other tenants' data
   ✓ Maximum security achieved!
```

---

## 📞 COMMON QUESTIONS

**Q: Can a user from shop1 edit users in shop2?**
A: No. CompanyScope global scope automatically filters queries by company_id. 404 is thrown if user doesn't belong to current tenant.

**Q: What happens on invalid role selection?**
A: Form request validates role must be 'admin', 'cashier', or 'user'. Failed validation shows error.

**Q: How is password stored?**
A: Hashed with bcrypt using Laravel's Hash::make(). Original password is never stored.

**Q: Can admin recover a deleted user?**
A: No. `destroy()` permanently deletes the user. Consider adding soft deletes for recovery capability.

**Q: What if form validation fails?**
A: User is redirected back to form with errors displayed. Old values are preserved in form fields.

**Q: How does the helper know which tenant to route to?**
A: `adminRoute()` helper in AdminHelper.php automatically:
1. Gets subdomain from authenticated user's company
2. Gets port from current request
3. Generates full URL with proper host and port

---

## 🎓 CODE EXAMPLES

### Adding New Validation Rule (Frontend only)
```blade
<!-- in create.blade.php or edit.blade.php -->
<div class="mb-4">
    <label class="label">Date of Birth</label>
    <input type="date" name="dob" value="{{ old('dob') }}" class="form-control">
</div>
```

Then in form request:
```php
'dob' => 'nullable|date|before:18 years ago',
```

### Accessing User Data in Controller
```php
$user->id;           // User ID
$user->name;         // User name
$user->email;        // Email address
$user->mobile;       // Phone number
$user->role;         // Role (string: admin/cashier/user)
$user->role_id;      // Role FK (integer ID)
$user->status;       // Active/inactive (boolean)
$user->company_id;   // Tenant company ID
$user->company;      // Tenant company object
```

### Displaying User in Blade
```blade
<h2>{{ $user->name }}</h2>
<p>Email: {{ $user->email }}</p>
<p>Phone: {{ $user->mobile }}</p>
<p>Role: {{ ucfirst($user->role) }}</p>
<p>Status: {{ $user->status ? 'Active' : 'Inactive' }}</p>

<!-- Link to edit -->
<a href="{{ adminRoute('admin.users.edit', ['user' => $user->id]) }}">Edit</a>
```

---

## 🆘 GETTING HELP

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Run Tests
```bash
php artisan test
```

### Clear Cache
```bash
php artisan view:cache
php artisan cache:clear
php artisan config:cache
```

### Fresh Start
```bash
php artisan migrate:fresh --seed
```

---

## ✨ THAT'S IT!

You're ready to go. The system handles:
- ✅ Tenant isolation
- ✅ Validation
- ✅ Security
- ✅ Error handling
- ✅ User management

Just use the URLs and it works!
