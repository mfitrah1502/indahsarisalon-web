<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_id',
        'name',
        'duration',
        'description',
        'price',
        'has_stylist_price',
        'price_senior',
        'price_junior',
    ];

    // Relasi ke Treatment
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    // Relasi ke HairLengthPrice (jika ada)
    public function hairLengthPrices()
    {
        return $this->hasMany(HairLengthPrice::class);
    }
}