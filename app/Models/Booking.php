<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    protected $primaryKey = 'booking_ID'; // Primary key column
    protected $table = 'bookings'; // Table name

    protected $fillable = [
        'member_ID', 
        'employee_ID', 
        'location', 
        'message', 
        'bookingDate', 
        'bookingTime',
        'statusBooking'
    ];

    public function member()
{
    return $this->belongsTo(Member::class, 'member_ID', 'member_ID');
}

    public function employee() {
        
        return $this->belongsTo(Employee::class, 'employee_ID', 'employee_ID');
    }
}
