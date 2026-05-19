<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
        $this->middleware('auth');
    }

    /**
     * Show the enable 2FA form with phone input
     */
    public function showEnable()
    {
        $user = Auth::user();
        
        return view('auth.two-factor.enable', [
            'user' => $user,
        ]);
    }

    /**
     * Enable 2FA for the authenticated user
     */
    public function enable(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|phone|unique:users,two_factor_phone|regex:/^(\+?254|0)[1-9]\d{8}$/',
        ], [
            'phone.required' => 'Phone number is required.',
            'phone.phone' => 'Please enter a valid phone number.',
            'phone.unique' => 'This phone number is already registered with another account.',
            'phone.regex' => 'Please enter a valid Kenyan phone number (e.g., 0712345678 or +254712345678).',
        ]);

        $user = Auth::user();

        // Enable 2FA and store phone
        $this->twoFactorService->enable($user, $validated['phone']);

        // Generate and send OTP
        $this->twoFactorService->generateAndSendOtp($user);

        return redirect()->route('user.two-factor.verify-setup')
            ->with('success', 'Verification code sent to your phone. Please enter it below.');
    }

    /**
     * Show the verification form during 2FA setup
     */
    public function showVerifySetup()
    {
        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('user.account')
                ->withErrors('Two-factor authentication is not enabled.');
        }

        return view('auth.two-factor.verify-setup', [
            'user' => $user,
        ]);
    }

    /**
     * Verify the OTP during setup
     */
    public function verifySetup(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Verification code is required.',
            'code.size' => 'Verification code must be 6 digits.',
        ]);

        $user = Auth::user();

        if (!$this->twoFactorService->verifyOtpCode($user, $validated['code'])) {
            return redirect()->route('user.two-factor.verify-setup')
                ->withErrors('Invalid or expired verification code. Please try again.');
        }

        return redirect()->route('user.account')
            ->with('success', 'Two-factor authentication has been successfully enabled!');
    }

    /**
     * Disable 2FA for the authenticated user
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $this->twoFactorService->disable($user);

        return redirect()->route('user.account')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show the OTP verification form during login
     */
    public function showLoginVerify()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor.verify-login');
    }

    /**
     * Verify OTP during login
     */
    public function verifyLogin(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Verification code is required.',
            'code.size' => 'Verification code must be 6 digits.',
        ]);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->twoFactorService->verifyOtpCode($user, $validated['code'])) {
            return redirect()->route('two-factor.login-verify')
                ->withErrors('Invalid or expired verification code. Please try again.');
        }

        // Clear 2FA session and authenticate user
        session()->forget('2fa_user_id');
        Auth::login($user, $request->boolean('remember'));

        return redirect()->intended();
    }

    /**
     * Resend OTP code (during login or setup)
     */
    public function resendCode(Request $request)
    {
        $userId = session('2fa_user_id');
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        if (!$userId) {
            return response()->json(['error' => 'Invalid session'], 400);
        }

        $user = User::find($userId);
        if (!$user || !$user->two_factor_enabled) {
            return response()->json(['error' => 'Invalid user'], 400);
        }

        $this->twoFactorService->generateAndSendOtp($user);

        return response()->json([
            'success' => true,
            'message' => 'New verification code sent to your phone.',
        ]);
    }
}
