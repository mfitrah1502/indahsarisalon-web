<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

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