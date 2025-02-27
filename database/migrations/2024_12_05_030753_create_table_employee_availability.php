<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_employee_availability', function (Blueprint $table) {
            $table->id('availability_ID');
            $table->foreignId('employee_ID')->constrained('employees', 'employee_ID');
            $table->string('day'); // e.g., Monday, Tuesday
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_closed')->default(false); // Closed or not
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_employee_availability');
    }
};
