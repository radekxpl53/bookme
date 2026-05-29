<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_blacklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses');
            $table->foreignId('user_id')->constrained('users');
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['business_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_blacklist');
    }
};
