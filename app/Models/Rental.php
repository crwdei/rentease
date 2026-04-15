<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    // Adjust these if your migration uses different column names
   protected $fillable = [
    'title',
    'description',
    'type',
    'price_per_day',
    'is_available',
    'company_id',
    'lessor_id',
];


    protected $casts = [
        'is_available' => 'boolean',
    ];

    // Relationships (optional now, useful later)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lessor()
    {
        return $this->belongsTo(User::class, 'lessor_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
