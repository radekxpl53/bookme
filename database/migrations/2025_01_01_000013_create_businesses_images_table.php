<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('images_id')->constrained('images');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses_images');
    }
};
