<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

   protected $fillable = [
    'rental_id',
    'client_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'start_date',
    'end_date',
    'total_price',
    'status',
    'notes',
    'valid_id_path',
    'gcash_reference',
    'gcash_amount',
];


    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'total_price' => 'decimal:2',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
