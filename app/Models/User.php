<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'gcash_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Customer bookings (as a client)
    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'client_id');
    }

    // Company relationship
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Lessor relationships
    public function rentals()
    {
        return $this->hasMany(\App\Models\Rental::class, 'lessor_id');
    }

    // Lessor bookings (bookings for the lessor's properties)
    public function lessorBookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'lessor_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLessor(): bool
    {
        return $this->role === 'lessor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}