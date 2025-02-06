<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAvailability extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'table_employee_availability';

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'availability_ID'; // Update with the actual primary key column

    // If the table has timestamps (created_at, updated_at), you can set this to false if it's not being used
    public $timestamps = false;

    // Define fillable or guarded properties
    protected $fillable = [
        'employee_ID',
        'day',
        'start_time',
        'end_time',
        'is_closed'
    ];

    // Optionally, you can add relationships if you have related models
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_ID');
    }
}

