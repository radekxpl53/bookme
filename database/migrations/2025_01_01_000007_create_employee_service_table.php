<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_service', function (Blueprint $table) {
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('service_id')->constrained('services');
            $table->primary(['employee_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_service');
    }
};
