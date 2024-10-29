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
        Schema::create('kundali_matching_report_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('male_kundli_id');
            $table->bigInteger('female_kundli_id');
            $table->string('boy_birthDate')->nullable();
            $table->string('boy_birthTime')->nullable();
            $table->string('boy_timezone')->nullable();
            $table->string('boy_latitude')->nullable();
            $table->string('boy_longitude')->nullable();
            $table->string('girl_birthDate')->nullable();
            $table->string('girl_birthTime')->nullable();
            $table->string('girl_timezone')->nullable();
            $table->string('girl_latitude')->nullable();
            $table->string('girl_longitude')->nullable();
            $table->json('girl_manglik_report_data')->nullable();
            $table->json('boy_manglik_report_data')->nullable();
            $table->json('ashtakoot_horoscope_data')->nullable();
            $table->json('dashakoot_horoscope_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kundali_matching_report_data');
    }
};
