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

        // Collect from details
        foreach ($this->details as $detail) {
            if ($detail->image_url && !in_array($detail->image_url, $images)) {
                $images[] = $detail->image_url;
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

}