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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_ID');
            $table->date('bookingDate');
            $table->time('bookingTime');
            $table->enum('statusBooking', ['pending', 'completed', 'confirmed', 'cancelled'])->default('pending');
            $table->text('message');
            $table->text('location');
            $table->foreignId('member_ID')->constrained('members', 'member_ID')->onDelete('cascade');
            $table->foreignId('employee_ID')->constrained('employees', 'employee_ID')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
