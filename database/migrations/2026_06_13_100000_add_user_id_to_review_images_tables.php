<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesse_reviews_images', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('business_id')->constrained('users');
        });

        Schema::table('employee_reviews_images', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('employee_id')->constrained('users');
        });
    }

    public function down(): void
    {
        Schema::table('businesse_reviews_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('employee_reviews_images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
