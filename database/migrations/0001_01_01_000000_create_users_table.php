<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('first_name');
            $table->string('surname');
            $table->string('phone')->nullable();
            $table->string('password');
            // image_id dodamy kluczem obcym pozniej, bo tabela images
            // jest tworzona w osobnej migracji
            $table->unsignedBigInteger('image_id')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
