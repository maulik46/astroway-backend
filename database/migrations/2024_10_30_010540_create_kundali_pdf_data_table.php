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
        Schema::create('kundali_pdf_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('kundali_id');
            
            $table->string('name')->nullable();
            $table->string('date')->nullable();
            $table->string('birthDate')->nullable();
            $table->string('birthTime')->nullable();
            $table->string('timezone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('language')->nullable();
            $table->string('planet')->nullable();
            $table->string('mahadasha')->nullable();
            $table->string('antardasha')->nullable();
            $table->string('paryantardasha')->nullable();
            $table->string('shookshamadasha')->nullable();

            $table->json('panchang_data')->nullable();
            $table->json('planet_details_data')->nullable();
            $table->json('ascendant_report_data')->nullable();
            $table->json('planet_report_data')->nullable();
            $table->json('find_moon_sign_data')->nullable();
            $table->json('find_sun_sign_data')->nullable();
            $table->json('find_ascendant_data')->nullable();
            $table->json('extended_kundli_details_data')->nullable();
            $table->json('gem_suggestion_data')->nullable();
            $table->json('numero_table_data')->nullable();
            $table->json('rudraksh_suggestion_data')->nullable();
            $table->json('shad_bala_data')->nullable();
            $table->json('friendship_table_data')->nullable();
            $table->json('kp_houses_data')->nullable();
            $table->json('kp_planets_data')->nullable();
            $table->json('mangal_dosh_data')->nullable();
            $table->json('kaalsarp_dosh_data')->nullable();
            $table->json('mangalik_dosh_data')->nullable();
            $table->json('pitra_dosh_data')->nullable();
            $table->json('papasamaya_data')->nullable();
            $table->json('current_sade_sati_data')->nullable();
            $table->json('sade_sati_table_data')->nullable();
            $table->json('varshapal_details_data')->nullable();
            $table->json('varshapal_month_chart_data')->nullable();
            $table->json('varshapal_year_chart_data')->nullable();
            $table->json('yoga_list_data')->nullable();
            $table->json('char_dasha_current_data')->nullable();
            $table->json('char_dasha_main_data')->nullable();
            $table->json('char_dasha_sub_data')->nullable();
            $table->json('yogini_dasha_main_data')->nullable();
            $table->json('yogini_dasha_sub_data')->nullable();
            $table->json('specific_dasha_data')->nullable();
            $table->json('current_mahadasha_full_data')->nullable();
            $table->json('current_mahadasha_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kundali_pdf_data');
    }
};
