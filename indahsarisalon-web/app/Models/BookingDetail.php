<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'treatment_detail_id', 'price'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function treatmentDetail()
    {
        return $this->belongsTo(TreatmentDetail::class);
    }
}
