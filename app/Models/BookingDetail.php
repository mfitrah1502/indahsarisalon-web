<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'treatment_detail_id', 'stylist_id', 'price'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function stylist()
    {
        return $this->belongsTo(User::class, 'stylist_id');
    }

    public function treatmentDetail()
    {
        return $this->belongsTo(TreatmentDetail::class);
    }
}
