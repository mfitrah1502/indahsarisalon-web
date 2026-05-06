<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    
    protected $fillable = [
        'amount',
        'category',
        'description',
        'expense_date',
    ];

    public $timestamps = false; // Using custom created_at from DB if needed
}
