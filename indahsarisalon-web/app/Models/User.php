<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'email_verified_at',
        'remember_token',
        'role',   
        'type',
        'kategori',
        'status',
        'avatar',
        'phone',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

    /**
     * Get the user's avatar URL.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
                return $this->avatar;
            }
            
            // Brute force: ambil elemen terakhir dari path (hanya nama filenya)
            $parts = explode('/', $this->avatar);
            $filename = end($parts);
            
            $baseUrl = config('services.supabase.url');
            if (!$baseUrl) return asset('assets/images/user/avatar-2.jpg');

            return rtrim($baseUrl, '/') . '/storage/v1/object/public/avatars/' . $filename;
        }

        return asset('assets/images/user/avatar-2.jpg');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
   public function absensi()
    {
        return $this->hasMany(\App\Models\Absensi::class, 'user_id', 'id');
    }
    
}

