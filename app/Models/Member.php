<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'member_ID'; // Primary key column
    protected $table = 'members'; // Table name

    // Fillable fields for mass assignment
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'lastName',
        'address',
        'phone',
        'dateOfBirth',
    ];

    // Hidden fields (e.g., for security)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'member_ID', 'member_ID');
    }
}