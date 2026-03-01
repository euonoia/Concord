<?php

namespace App\Models\admin\Hr\hr3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceQrToken extends Model
{
    use HasFactory;

    protected $table = 'attendance_qr_tokens_hr3';

    protected $fillable = [
        'token',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}