<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->foreignId('user_id')->constrained('users');
            $table->text('service')->nullable();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_reviews');
    }
};
