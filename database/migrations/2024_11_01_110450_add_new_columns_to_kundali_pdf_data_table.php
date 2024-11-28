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
        Schema::table('kundali_pdf_data', function (Blueprint $table) {
            $table->string('divChart')->nullable();
            $table->string('divChartResponseType')->nullable();
            $table->string('divChartTransitDate')->nullable();
            $table->string('divChartYear')->nullable();
            $table->string('chartImageColor')->nullable();
            $table->string('chartImageStyle')->nullable();
            $table->string('chartImageFontSize')->nullable();
            $table->string('chartImageFontStyle')->nullable();
            $table->string('chartImageSize')->nullable();
            $table->string('chartImageStroke')->nullable();
            $table->string('chartImageFormat')->nullable();
            $table->json('d1Chart_data')->nullable();
            $table->json('d9Chart_data')->nullable();
            $table->json('mahaDasha_data')->nullable();
            $table->json('antarDasha_data')->nullable();
            $table->json('paryantarDasha_data')->nullable();
            $table->json('mahaDashaPredictions_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kundali_pdf_data', function (Blueprint $table) {
            //
        });
    }
};
