<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
    protected $appends = ['avatar_url', 'tier', 'total_spending'];

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

        return asset('assets/images/user/default-avatar.svg');
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

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'user_id', 'id');
    }

    /**
     * Get total successful spending
     */
    public function getTotalSpendingAttribute()
    {
        if (!isset($this->attributes['cached_total_spending'])) {
            $this->attributes['cached_total_spending'] = $this->bookings()
                ->where('status', 'berhasil')
                ->where('payment_status', 'paid')
                ->sum('total_price');
        }
        return $this->attributes['cached_total_spending'];
    }

    /**
     * Get Membership Tier
     */
    public function getTierAttribute()
    {
        $total = $this->total_spending;

        if ($total > 3000000) return 'Platinum';
        if ($total > 2000000) return 'Gold';
        if ($total > 1000000) return 'Silver';
        
        return 'Regular';
    }

    /**
     * Get General Tier Discount Percentage
     */
    public function getTierDiscountAttribute()
    {
        return 0; // Tiers are informational only, no general discount
    }

    /**
     * Check for Coloring Loyalty (Spend > 1.5M on Coloring in last 2 years)
     */
    public function getHasColoringLoyaltyAttribute()
    {
        if (!isset($this->attributes['cached_coloring_loyalty'])) {
            // Hitung pengeluaran khusus kategori 'Coloring'
            $this->attributes['cached_coloring_loyalty'] = \App\Models\BookingDetail::whereHas('booking', function($q) {
                    $q->where('user_id', $this->id)
                      ->where('status', 'berhasil')
                      ->where('payment_status', 'paid')
                      ->where('reservation_datetime', '>=', now()->subYears(2));
                })
                ->whereHas('treatmentDetail.treatment.category', function($q) {
                    $q->where('name', 'like', '%Coloring%');
                })
                ->sum('price') >= 1500000;
        }
        return $this->attributes['cached_coloring_loyalty'];
    }

    /**
     * Progress to Next Tier
     */
    public function getNextTierInfoAttribute()
    {
        $total = $this->total_spending;
        
        if ($total > 3000000) {
            return ['next' => null, 'needed' => 0, 'percent' => 100];
        }
        
        if ($total > 2000000) {
            $needed = 3000000 - $total;
            $percent = (($total - 2000000) / 1000000) * 100;
            return ['next' => 'Platinum', 'needed' => $needed, 'percent' => $percent];
        }
        
        if ($total > 1000000) {
            $needed = 2000000 - $total;
            $percent = (($total - 1000000) / 1000000) * 100;
            return ['next' => 'Gold', 'needed' => $needed, 'percent' => $percent];
        }
        
        $needed = 1000000 - $total;
        $percent = ($total / 1000000) * 100;
        return ['next' => 'Silver', 'needed' => $needed, 'percent' => $percent];
    }
    
}
