<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_reviews_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('images_id')->constrained('images');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_reviews_images');
    }
};
