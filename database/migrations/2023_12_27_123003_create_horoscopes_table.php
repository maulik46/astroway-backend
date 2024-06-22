<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horoscopes', function (Blueprint $table) {
            $table->id();
            $table->string('zodiac');
            $table->integer('total_score');
            $table->string('lucky_color');
            $table->string('lucky_color_code');
            $table->json('lucky_number');
            $table->integer('physique');
            $table->integer('status');
            $table->integer('finances');
            $table->integer('relationship');
            $table->integer('career');
            $table->integer('travel');
            $table->integer('family');
            $table->integer('friends');
            $table->integer('health');
            $table->text('bot_response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horoscopes');
    }
};
