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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_ID');
            $table->string('firstName', 30);
            $table->string('lastName', 30);
            $table->string('email', 40)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('status', ['active', 'inactive']);
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 15)->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->text('coverageArea')->nullable();
            $table->foreignId('service_ID')->nullable()->constrained('services', 'service_ID')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
