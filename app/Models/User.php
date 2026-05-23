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
        'nickname', 
        'birth_place', 
        'birth_date', 
        'gender', 
        'position', 
        'division', 
        'join_date', 
        'employment_status', 
        'emergency_contact', 
        'bank_account_name', 
        'bank_account_number', 
        'last_education', 
        'membership_tier', 
        'total_spend', 
        'last_transaction_at'
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
     * Get all bookings matching user ID, email, or phone (to handle disconnected bookings)
     */
    public function getAllBookingsQuery()
    {
        if (is_string($this->id) && strpos($this->id, 'guest-') === 0) {
            return \App\Models\Booking::where(function ($q) {
                $hasIdentifier = false;
                if (!empty($this->email) && $this->email !== '-') {
                    $q->orWhere('customer_email', $this->email);
                    $hasIdentifier = true;
                }
                if (!empty($this->phone) && $this->phone !== '-') {
                    $q->orWhere('customer_phone', $this->phone);
                    $hasIdentifier = true;
                }
                if (!empty($this->name) && $this->name !== '-') {
                    $q->orWhere('customer_name', $this->name);
                    $hasIdentifier = true;
                }
                if (!$hasIdentifier) {
                    $q->whereRaw('1=0');
                }
            });
        }

        return \App\Models\Booking::where(function ($q) {
            $q->where('user_id', (int)$this->id);
            if (!empty($this->email) && $this->email !== '-') {
                $q->orWhere('customer_email', $this->email);
            }
            if (!empty($this->phone) && $this->phone !== '-') {
                $q->orWhere('customer_phone', $this->phone);
            }
        });
    }

    /**
     * Get total successful spending
     */
    public function getTotalSpendingAttribute()
    {
        if (!isset($this->attributes['cached_total_spending'])) {
            $this->attributes['cached_total_spending'] = $this->getAllBookingsQuery()
                ->where('status', 'success')
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
        if (in_array(strtolower($this->role), ['admin', 'owner', 'karyawan'])) {
            return 'Regular';
        }

        $total = $this->total_spending;

        if ($total >= 3000000) return 'Platinum';
        if ($total >= 2000000) return 'Gold';
        if ($total >= 1000000) return 'Silver';
        
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
     * Get Last Transaction Date
     */
    public function getLastTransactionAtAttribute()
    {
        if (!isset($this->attributes['cached_last_transaction_at'])) {
            $latestBooking = $this->getAllBookingsQuery()
                ->where('status', 'success')
                ->latest('reservation_datetime')
                ->first();
            $this->attributes['cached_last_transaction_at'] = $latestBooking ? $latestBooking->reservation_datetime : null;
        }
        return $this->attributes['cached_last_transaction_at'];
    }

    /**
     * Get total spending in the last 2 years
     */
    public function getRecentSpendingAttribute()
    {
        if (!isset($this->attributes['cached_recent_spending'])) {
            $this->attributes['cached_recent_spending'] = $this->getAllBookingsQuery()
                ->where('status', 'success')
                ->where('payment_status', 'paid')
                ->where('reservation_datetime', '>=', now()->subYears(2))
                ->sum('total_price');
        }
        return $this->attributes['cached_recent_spending'];
    }

    /**
     * Check for Coloring Loyalty (Spend >= 1.5M in the last 2 years)
     */
    public function getHasColoringLoyaltyAttribute()
    {
        if (in_array(strtolower($this->role), ['admin', 'owner', 'karyawan'])) {
            return false;
        }

        return $this->recent_spending >= 1500000;
    }

    /**
     * Check if user is a Colour Circle member (has coloring loyalty and active status)
     */
    public function getIsColourCircleMemberAttribute()
    {
        return $this->has_coloring_loyalty;
    }

    /**
     * Progress to Next Tier
     */
    public function getNextTierInfoAttribute()
    {
        $total = $this->total_spending;
        
        if ($total >= 3000000) {
            return ['next' => null, 'needed' => 0, 'percent' => 100];
        }
        
        if ($total >= 2000000) {
            $needed = 3000000 - $total;
            $percent = (($total - 2000000) / 1000000) * 100;
            return ['next' => 'Platinum', 'needed' => $needed, 'percent' => $percent];
        }
        
        if ($total >= 1000000) {
            $needed = 2000000 - $total;
            $percent = (($total - 1000000) / 1000000) * 100;
            return ['next' => 'Gold', 'needed' => $needed, 'percent' => $percent];
        }
        
        $needed = 1000000 - $total;
        $percent = ($total / 1000000) * 100;
        return ['next' => 'Silver', 'needed' => $needed, 'percent' => $percent];
    }
    
}
