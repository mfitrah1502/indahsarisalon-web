<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    // Tabel yang digunakan
    protected $table = 'password_resets';

    // Field yang boleh diisi mass-assignment
    protected $fillable = [
        'email',
        'otp',
        'expires_at'
    ];
    public $timestamps = true; // Nonaktifkan timestamps jika tidak diperlukan

    // Nonaktifkan auto-increment karena kita tidak perlu id otomatis (opsional)
    public $incrementing = false;
    protected $primaryKey = 'email';
}