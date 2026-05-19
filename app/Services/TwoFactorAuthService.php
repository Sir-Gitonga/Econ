<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class TwoFactorAuthService
{
    /**
     * Generate a random 6-digit OTP code
     */
    public function generateOtpCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate and send OTP to user's phone
     */
    public function generateAndSendOtp(User $user): bool
    {
        if (!$user->two_factor_enabled || !$user->two_factor_phone) {
            return false;
        }

        $code = $this->generateOtpCode();
        
        // Store the code and set expiration (5 minutes)
        $user->update([
            'two_factor_code' => $code,
            'two_factor_code_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Send SMS
        return $this->sendOtpViaSms($user->two_factor_phone, $code);
    }

    /**
     * Verify the OTP code entered by user
     */
    public function verifyOtpCode(User $user, string $code): bool
    {
        if (!$user->two_factor_code) {
            return false;
        }

        // Check if code has expired
        if ($user->two_factor_code_expires_at && $user->two_factor_code_expires_at->isPast()) {
            $user->update(['two_factor_code' => null]);
            return false;
        }

        // Check if code matches
        if ($user->two_factor_code !== $code) {
            return false;
        }

        // Code verified - clear it
        $user->update(['two_factor_code' => null]);
        
        return true;
    }

    /**
     * Send OTP via SMS
     * This uses the SMS settings configured in CompanySmsSetting
     */
    private function sendOtpViaSms(string $phone, string $code): bool
    {
        try {
            // Get SMS settings - you can configure this based on your implementation
            // For now, this is a placeholder that should be implemented based on your SMS provider
            $message = "Your verification code is: {$code}. Valid for 5 minutes.";
            
            // TODO: Implement actual SMS sending using configured provider
            // You can use AfricasTalking, Twilio, etc.
            
            // For development/testing, you might want to log this
            \Log::info("2FA SMS would be sent to {$phone}: {$message}");
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send 2FA OTP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enable 2FA for a user
     */
    public function enable(User $user, string $phone): bool
    {
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_phone' => $phone,
        ]);

        return true;
    }

    /**
     * Disable 2FA for a user
     */
    public function disable(User $user): bool
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_phone' => null,
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
        ]);

        return true;
    }
}
