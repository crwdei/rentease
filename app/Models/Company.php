<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'owner_name',
    'email',
    'phone',
    'address',
    'status',
    'gcash_number',
];

    // Optional, assuming rentals table has company_id
     // All lessor users under this company
    public function lessors()
    {
        return $this->hasMany(User::class, 'company_id')
                    ->where('role', 'lessor');
    }

    // All rentals owned by this company
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'company_id');
    }
}
