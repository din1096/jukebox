<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // gekozen naam
            $table->json('songs');  // array of song IDs
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_playlists');
    }
};