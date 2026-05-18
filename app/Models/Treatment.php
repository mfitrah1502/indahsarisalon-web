<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'is_promo',
        'promo_type',
        'promo_value',
        'image',
        'allow_multi_select',
        'is_active',
        'promo_start_date',
        'promo_end_date',
        'target_audience',
    ];
    
    protected $casts = [
        'promo_start_date' => 'date',
        'promo_end_date' => 'date',
        'is_promo' => 'boolean',
        'is_active' => 'boolean',
        'allow_multi_select' => 'boolean',
    ];

    protected $appends = ['main_image_url', 'all_images'];

    /**
     * Get main image URL
     */
    public function getMainImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('assets/images/no-image.jpg');
        }

        if (strpos($this->image, 'http') === 0) {
            return $this->image;
        }

        $bucket = ($this->is_promo && env('SUPABASE_PROMO_BUCKET')) ? env('SUPABASE_PROMO_BUCKET') : env('SUPABASE_BUCKET');
        $baseUrl = env('SUPABASE_URL');

        if (!$baseUrl) {
            return asset('assets/images/no-image.jpg');
        }

        return $baseUrl . '/storage/v1/object/public/' . $bucket . '/' . $this->image;
    }

    /**
     * Get all images (main + details)
     */
    public function getAllImagesAttribute()
    {
        $images = [];

        // Use direct query to avoid loading models and causing infinite recursion
        // when TreatmentDetails are serialized and try to access parent Treatment
        $detailImages = \Illuminate\Support\Facades\DB::table('treatment_details')
            ->where('treatment_id', $this->id)
            ->whereNotNull('image_url')
            ->where('image_url', '!=', '')
            ->pluck('image_url')
            ->toArray();

        foreach ($detailImages as $url) {
            if (!in_array($url, $images)) {
                $images[] = $url;
            }
        }

        // If no detail images, use main image
        if (count($images) == 0) {
            $images[] = $this->main_image_url;
        }

        return $images;
    }

    // Relasi ke detail
    public function details()
    {
        return $this->hasMany(TreatmentDetail::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Check if the promo matches the given user based on target audience
     */
    public function matchesUser($user = null)
    {
        $audience = strtolower($this->target_audience ?: 'general');
        
        if (in_array($audience, ['general', 'semua (general)', 'semua'])) {
            return true;
        }
        
        $currentUser = $user ?? auth()->user();
        if (!$currentUser) {
            return false;
        }
        
        if (in_array($audience, ['komunitas (grup awal)', 'komunitas'])) {
            return true;
        }
        
        $userTier = strtolower($currentUser->tier); // 'regular', 'silver', 'colour circle', 'gold', 'platinum'
        
        $targetTier = 'regular';
        if (strpos($audience, 'silver') !== false) {
            $targetTier = 'silver';
        } elseif (strpos($audience, 'colour circle') !== false) {
            $targetTier = 'colour circle';
        } elseif (strpos($audience, 'gold') !== false) {
            $targetTier = 'gold';
        } elseif (strpos($audience, 'platinum') !== false) {
            $targetTier = 'platinum';
        }
        
        return $userTier === $targetTier;
    }
}