<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OTP extends Model
{
    protected $table = 'otps';

    protected $fillable = [
        'identifier',
        'otp_code',
        'expires_at',
        'attempts',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Check if the OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP is valid (not expired and matches).
     */
    public static function isValid(string $identifier, string $code): bool
    {
        return self::where('identifier', $identifier)
            ->where('otp_code', $code)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }
}
