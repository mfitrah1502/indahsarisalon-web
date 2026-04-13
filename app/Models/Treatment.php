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