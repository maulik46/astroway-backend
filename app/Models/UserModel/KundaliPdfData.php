<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KundaliPdfData extends Model
{
    use HasFactory;

    protected $table = 'kundali_pdf_data';

    protected $fillable = [
        'kundali_id',
        'name',
        'date',
        'birthDate',
        'birthTime',
        'timezone',
        'latitude',
        'longitude',
        'language',
        'planet',
        'mahadasha',
        'antardasha',
        'paryantardasha',
        'shookshamadasha',
        'panchang_data',
        'planet_details_data',
        'ascendant_report_data',
        'planet_report_data',
        'find_moon_sign_data',
        'find_sun_sign_data',
        'find_ascendant_data',
        'extended_kundli_details_data',
        'gem_suggestion_data',
        'numero_table_data',
        'rudraksh_suggestion_data',
        'shad_bala_data',
        'friendship_table_data',
        'kp_houses_data',
        'kp_planets_data',
        'mangal_dosh_data',
        'kaalsarp_dosh_data',
        'mangalik_dosh_data',
        'pitra_dosh_data',
        'papasamaya_data',
        'current_sade_sati_data',
        'sade_sati_table_data',
        'varshapal_details_data',
        'varshapal_month_chart_data',
        'varshapal_year_chart_data',
        'yoga_list_data',
        'char_dasha_current_data',
        'char_dasha_main_data',
        'char_dasha_sub_data',
        'yogini_dasha_main_data',
        'yogini_dasha_sub_data',
        'specific_dasha_data',
        'current_mahadasha_full_data',
        'current_mahadasha_data',
    ];
}
