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
        'image_url',
    ];

    protected $appends = ['calculated_price', 'display_price_range'];

    public function getCalculatedPriceAttribute()
    {
        return $this->getCalculatedPrice();
    }

    public function getDisplayPriceRangeAttribute()
    {
        $prices = $this->getMinMaxCalculatedPrice();
        if ($prices['min'] == $prices['max']) {
            return 'Rp ' . number_format($prices['min'], 0, ',', '.');
        }
        return 'Rp ' . number_format($prices['min'], 0, ',', '.') . ' - ' . number_format($prices['max'], 0, ',', '.');
    }

    // Relasi ke Treatment
    public function treatment()
    {
        return $this->belongsTo(Treatment::class)->withTrashed();
    }

    /**
     * Calculate price after promo and loyalty
     */
    public function getCalculatedPrice($user = null)
    {
        $treatment = $this->treatment;
        $originalPrice = $this->price;
        $isPromo = $treatment->is_promo;
        $promoType = $treatment->promo_type;
        $promoValue = $treatment->promo_value;

        $finalPrice = (float)$originalPrice;

        // Apply Promo
        if ($isPromo) {
            $pType = strtolower($promoType);
            if (in_array($pType, ['percentage', 'percent', 'persen'])) {
                $finalPrice -= ($finalPrice * $promoValue / 100);
            } else {
                $finalPrice = (float)$promoValue;
            }
        }

        // Apply Loyalty (Coloring 35%)
        $currentUser = $user ?? auth()->user();
        if ($currentUser) {
            $isColoring = $treatment->category && stripos($treatment->category->name, 'Coloring') !== false;
            if ($isColoring && $currentUser->has_coloring_loyalty) {
                $finalPrice -= ($finalPrice * 35 / 100);
            }
        }

        return max(0, $finalPrice);
    }

    /**
     * Get min and max price for stylist prices
     */
    public function getMinMaxCalculatedPrice($user = null)
    {
        if (!$this->has_stylist_price) {
            $price = $this->getCalculatedPrice($user);
            return ['min' => $price, 'max' => $price];
        }

        $treatment = $this->treatment;
        $isPromo = $treatment->is_promo;
        $promoType = $treatment->promo_type;
        $promoValue = $treatment->promo_value;
        $currentUser = $user ?? auth()->user();

        $prices = array_filter([(int)$this->price_senior, (int)$this->price_junior]);
        $min = count($prices) > 0 ? min($prices) : (int)$this->price;
        $max = count($prices) > 0 ? max($prices) : (int)$this->price;

        $apply = function($p) use ($isPromo, $promoType, $promoValue, $treatment, $currentUser) {
            $val = (float)$p;
            if ($isPromo) {
                $pType = strtolower($promoType);
                if (in_array($pType, ['percentage', 'percent', 'persen'])) {
                    $val -= ($val * $promoValue / 100);
                } else {
                    $val = (float)$promoValue;
                }
            }
            if ($currentUser) {
                $isColoring = $treatment->category && stripos($treatment->category->name, 'Coloring') !== false;
                if ($isColoring && $currentUser->has_coloring_loyalty) {
                    $val -= ($val * 35 / 100);
                }
            }
            return max(0, $val);
        };

        return [
            'min' => $apply($min),
            'max' => $apply($max)
        ];
    }

    // Relasi ke HairLengthPrice (jika ada)
    public function hairLengthPrices()
    {
        return $this->hasMany(HairLengthPrice::class);
    }
}