<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HairLengthPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_detail_id',
        'hair_length', // short, medium, long, x-tra
        'price',
    ];

    // Relasi ke TreatmentDetail
    public function treatmentDetail()
    {
        return $this->belongsTo(TreatmentDetail::class);
    }
}