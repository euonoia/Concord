<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendOTPRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Models\OTP;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class OTPController extends Controller
{
    /*
    public function send(SendOTPRequest $request): JsonResponse
    {
        $identifier = $request->identifier;

        // Rate limiting: 3 requests per minute per identifier
        if (RateLimiter::tooManyAttempts('otp-send:' . $identifier, 3)) {
            return response()->json([
                'message' => 'Too many OTP requests. Please try again in ' . RateLimiter::availableIn('otp-send:' . $identifier) . ' seconds.',
            ], 429);
        }

        // Invalidate any previous active OTPs for this identifier
        OTP::where('identifier', $identifier)->where('is_used', false)->update(['is_used' => true]);

        // Generate a 6-digit numeric OTP cryptographically secure
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the OTP
        OTP::create([
            'identifier' => $identifier,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(5),
            'attempts' => 0,
            'is_used' => false,
        ]);

        // Send the OTP via Email (using SMTP configured in .env)
        try {
            Mail::to($identifier)->send(new OTPMail($otpCode));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        RateLimiter::hit('otp-send:' . $identifier, 60);

        return response()->json([
            'message' => 'OTP sent successfully to ' . $identifier,
        ]);
    }

    public function verify(VerifyOTPRequest $request): JsonResponse
    {
        $identifier = $request->identifier;
        $otpCode = $request->otp_code;

        $otp = OTP::where('identifier', $identifier)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'No active OTP found for this identifier.'], 404);
        }

        // Check for brute-force: Maximum 5 verification attempts
        if ($otp->attempts >= 5) {
            $otp->update(['is_used' => true]); // Invalidate after too many attempts
            return response()->json(['message' => 'Maximum verification attempts exceeded. Please request a new OTP.'], 403);
        }

        // Check if expired
        if ($otp->isExpired()) {
            $otp->update(['is_used' => true]);
            return response()->json(['message' => 'OTP has expired. Please request a new one.'], 422);
        }

        // Verify the code
        if ($otp->otp_code !== $otpCode) {
            $otp->increment('attempts');
            return response()->json([
                'message' => 'Invalid OTP code.',
                'attempts_left' => 5 - $otp->attempts,
            ], 422);
        }

        // Success: Mark as used
        $otp->update(['is_used' => true]);

        return response()->json([
            'message' => 'OTP verified successfully.',
            // You might return a token or perform further actions here
        ]);
    }
    */
}
