<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('service_id')->constrained('services');
            $table->dateTime('start_at');
            $table->dateTime('finish_at');
            // status: pending, confirmed, completed, cancelled
            $table->string('status')->default('pending');
            $table->decimal('total_price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
