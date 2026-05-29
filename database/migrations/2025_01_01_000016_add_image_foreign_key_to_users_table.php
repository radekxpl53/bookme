<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dodajemy klucz obcy dopiero teraz, bo tabela images
        // jest tworzona po tabeli users
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('image_id')->references('id')->on('images');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
        });
    }
};
