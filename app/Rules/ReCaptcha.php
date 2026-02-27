<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReCaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip reCAPTCHA verification in local environment
        if (app()->environment('local', 'testing')) {
            return;
        }

        if (empty($value)) {
            $fail('reCAPTCHA verification is required.');
            return;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $value,
            ]);

            $body = $response->json();

            if (! ($body['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed.', ['response' => $body]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            if (($body['score'] ?? 0) < 0.5) {
                Log::warning('reCAPTCHA score too low.', ['score' => $body['score'] ?? 0]);
                $fail('Our system flagged this submission as suspicious. Please try again.');
                return;
            }

            if (($body['action'] ?? '') !== 'confirm_booking') {
                Log::warning('reCAPTCHA action mismatch.', ['action' => $body['action'] ?? '']);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error.', ['exception' => $e->getMessage()]);
            $fail('Unable to verify reCAPTCHA. Please try again later.');
        }
    }
}
