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
        Schema::table('horoscopes', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->text('health_remark')->default('');
            $table->text('career_remark')->default('');
            $table->text('relationship_remark')->default('');
            $table->text('travel_remark')->default('');
            $table->text('family_remark')->default('');
            $table->text('friends_remark')->default('');
            $table->text('finances_remark')->default('');
            $table->text('status_remark')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
