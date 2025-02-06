<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'employee_ID'; // Primary key column
    protected $table = 'employees'; // Table name

    // Fillable fields for mass assignment
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'status',
        'address',
        'description',
        'coverageArea',
        'available_date',
        'available_weekly_time',
        'available_Time',
        'dateOfBirth',
        'phone',
        'service_ID',
    ];

    protected $casts = [
        'availability' => 'array',
    ];

    // Hidden fields for security
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function service()
{
    return $this->belongsTo(Service::class, 'service_ID');
}

public function bookings()
{
    return $this->hasMany(Booking::class, 'employee_ID', 'employee_ID');
}
}
