<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\KundaliPdfData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;

class KundaliPdfDataController extends Controller
{
    public function getkundaliPdfdata(Request $req)
    {
        try {

            $api_key = Cache::get('vedicAstroAPI_key');
            
            if(!$api_key){
                // cache for 1 hour
                $api_key = Cache::remember('vedicAstroAPI_key', 3600, function () {
                    return DB::table('systemflag')->where('name', 'vedicAstroAPI')->value('value');
                });
            }

            if (!$api_key) {
                return response()->json([
                    'error' => false,
                    'message' => "Invalid Vedic Astro API",
                    'status' => 400,
                ], 400);
            }

            $requestData = $req->only([
                'kundali_id', 'name', 'date', 'birthDate', 'birthTime', 'timezone',
                'latitude', 'longitude', 'planet', 'language', 'mahadasha', 'antardasha',
                'paryantardasha', 'shookshamadasha', 'divChart', 'divChartResponseType',
                'divChartTransitDate', 'divChartYear', 'chartImageColor', 'chartImageStyle',
                'chartImageFontSize', 'chartImageFontStyle', 'chartImageSize', 'chartImageStroke',
                'chartImageFormat'
            ]);

            $requestData['date'] = date('d/m/Y', strtotime($requestData['date']));
            $requestData['birthDate'] = date('d/m/Y', strtotime($requestData['birthDate']));

            $kundali_pdf_data = KundaliPdfData::where($requestData)->orderByDesc('id')->first();

            if(!$kundali_pdf_data){
                // call API and save the response

                $responses = Http::pool(fn(Pool $pool) => $this->getApiCallResponse($pool, $requestData, $api_key));

                $apiErrors = [];
                    foreach($responses as $key => $response){
                        $res = $response->json() ?? [];
                        $hasStatus = array_key_exists('status',$res);
                        if($hasStatus && $response->json()['status'] != 200){
                            $apiErrors[] = [
                                'name' => $key,
                                'error' => $response->json()
                            ];
                        }
                    }

                if(count($apiErrors) > 1){
                    return response()->json([
                        'error' => true,
                        'message' => "Something went wrong",
                        'errorData' => $apiErrors,
                        'status' => 400,
                    ], 400);
                }

                $resData = collect($responses)
                    ->map(function($response, $key){
                        $bodyData = ['d9Chart'];
                        if(in_array($key, $bodyData)){
                            return [
                                'status' => $response->status(),
                                'response' => $response->body(),
                            ];

                        }
                        else {
                            return $response->json();
                        }
                    });

                KundaliPdfData::create(array_merge($requestData,[
                    
                    'panchang_data' => json_encode($resData['panchang']),
                    'planet_details_data' => json_encode($resData['planetDetails']),
                    'ascendant_report_data' => json_encode($resData['ascendantReport']),
                    'planet_report_data' => json_encode($resData['planetReport']),
                    'find_moon_sign_data' => json_encode($resData['findMoonSign']),
                    'find_sun_sign_data' => json_encode($resData['findSunSign']),
                    'find_ascendant_data' => json_encode($resData['findAcendant']),
                    'extended_kundli_details_data' => json_encode($resData['extendedKundliDetails']),
                    'gem_suggestion_data' => json_encode($resData['gemSuggestion']),
                    'numero_table_data' => json_encode($resData['numeroTable']),
                    'rudraksh_suggestion_data' => json_encode($resData['rudrakshSuggestion']),
                    'shad_bala_data' => json_encode($resData['shadBala']),
                    'friendship_table_data' => json_encode($resData['friendshipTable']),
                    'kp_houses_data' => json_encode($resData['kpHouses']),
                    'kp_planets_data' => json_encode($resData['kpPlanets']),
                    'mangal_dosh_data' => json_encode($resData['mangalDosh']),
                    'kaalsarp_dosh_data' => json_encode($resData['KaalsarpDosh']),
                    'mangalik_dosh_data' => json_encode($resData['managlikDosh']),
                    'pitra_dosh_data' => json_encode($resData['pitraDosh']),
                    'papasamaya_data' => json_encode($resData['papasamaya']),
                    'current_sade_sati_data' => json_encode($resData['currentSadeSati']),
                    'sade_sati_table_data' => json_encode($resData['sadeSatiTable']),
                    'varshapal_details_data' => json_encode($resData['varshapalDetails']),
                    'varshapal_month_chart_data' => json_encode($resData['varshapalMonthChart']),
                    'varshapal_year_chart_data' => json_encode($resData['varshapalYearChart']),
                    'yoga_list_data' => json_encode($resData['yogaList']),
                    'char_dasha_current_data' => json_encode($resData['charDashaCurrent']),
                    'char_dasha_main_data' => json_encode($resData['charDashaMain']),
                    'char_dasha_sub_data' => json_encode($resData['charDashaSub']),
                    'yogini_dasha_main_data' => json_encode($resData['yoginiDashaMain']),
                    'yogini_dasha_sub_data' => json_encode($resData['yoginiDashaSub']),
                    'specific_dasha_data' => json_encode($resData['specificDasha']),
                    'current_mahadasha_full_data' => json_encode($resData['currentMahadashaFull']),
                    'current_mahadasha_data' => json_encode($resData['currentMahadasha']),

                    'd1Chart_data' => json_encode($resData['d1Chart']),
                    'd9Chart_data' => json_encode($resData['d9Chart']),
                    'mahaDasha_data' => json_encode($resData['mahaDasha']),
                    'antarDasha_data' => json_encode($resData['antarDasha']),
                    'paryantarDasha_data' => json_encode($resData['paryantarDasha']),
                    'mahaDashaPredictions_data' => json_encode($resData['mahaDashaPredictions']),
                ]));

                $responseData = $resData;

            }
            else{
                // get saved response
                $responseData = [
                    'panchang' => json_decode($kundali_pdf_data->panchang_data, true) ?? null,
                    'planetDetails' => json_decode($kundali_pdf_data->planet_details_data, true) ?? null,
                    'ascendantReport' => json_decode($kundali_pdf_data->ascendant_report_data, true) ?? null,
                    'planetReport' => json_decode($kundali_pdf_data->planet_report_data, true) ?? null,
                    'findMoonSign' => json_decode($kundali_pdf_data->find_moon_sign_data, true) ?? null,
                    'findSunSign' => json_decode($kundali_pdf_data->find_sun_sign_data, true) ?? null,
                    'findAcendant' => json_decode($kundali_pdf_data->find_ascendant_data, true) ?? null,
                    'extendedKundliDetails' => json_decode($kundali_pdf_data->extended_kundli_details_data, true) ?? null,
                    'gemSuggestion' => json_decode($kundali_pdf_data->gem_suggestion_data, true) ?? null,
                    'numeroTable' => json_decode($kundali_pdf_data->numero_table_data, true) ?? null,
                    'rudrakshSuggestion' => json_decode($kundali_pdf_data->rudraksh_suggestion_data, true) ?? null,
                    'shadBala' => json_decode($kundali_pdf_data->shad_bala_data, true) ?? null,
                    'friendshipTable' => json_decode($kundali_pdf_data->friendship_table_data, true) ?? null,
                    'kpHouses' => json_decode($kundali_pdf_data->kp_houses_data, true) ?? null,
                    'kpPlanets' => json_decode($kundali_pdf_data->kp_planets_data, true) ?? null,
                    'mangalDosh' => json_decode($kundali_pdf_data->mangal_dosh_data, true) ?? null,
                    'KaalsarpDosh' => json_decode($kundali_pdf_data->kaalsarp_dosh_data, true) ?? null,
                    'managlikDosh' => json_decode($kundali_pdf_data->mangalik_dosh_data, true) ?? null,
                    'pitraDosh' => json_decode($kundali_pdf_data->pitra_dosh_data, true) ?? null,
                    'papasamaya' => json_decode($kundali_pdf_data->papasamaya_data, true) ?? null,
                    'currentSadeSati' => json_decode($kundali_pdf_data->current_sade_sati_data, true) ?? null,
                    'sadeSatiTable' => json_decode($kundali_pdf_data->sade_sati_table_data, true) ?? null,
                    'varshapalDetails' => json_decode($kundali_pdf_data->varshapal_details_data, true) ?? null,
                    'varshapalMonthChart' => json_decode($kundali_pdf_data->varshapal_month_chart_data, true) ?? null,
                    'varshapalYearChart' => json_decode($kundali_pdf_data->varshapal_year_chart_data, true) ?? null,
                    'yogaList' => json_decode($kundali_pdf_data->yoga_list_data, true) ?? null,
                    'charDashaCurrent' => json_decode($kundali_pdf_data->char_dasha_current_data, true) ?? null,
                    'charDashaMain' => json_decode($kundali_pdf_data->char_dasha_main_data, true) ?? null,
                    'charDashaSub' => json_decode($kundali_pdf_data->char_dasha_sub_data, true) ?? null,
                    'yoginiDashaMain' => json_decode($kundali_pdf_data->yogini_dasha_main_data, true) ?? null,
                    'yoginiDashaSub' => json_decode($kundali_pdf_data->yogini_dasha_sub_data, true) ?? null,
                    'specificDasha' => json_decode($kundali_pdf_data->specific_dasha_data, true) ?? null,
                    'currentMahadashaFull' => json_decode($kundali_pdf_data->current_mahadasha_full_data, true) ?? null,
                    'currentMahadasha' => json_decode($kundali_pdf_data->current_mahadasha_data, true) ?? null,
                    'mahaDasha' => json_decode($kundali_pdf_data->mahaDasha_data, true) ?? null,
                    'antarDasha' => json_decode($kundali_pdf_data->antarDasha_data, true) ?? null,
                    'paryantarDasha' => json_decode($kundali_pdf_data->paryantarDasha_data, true) ?? null,
                    'mahaDashaPredictions' => json_decode($kundali_pdf_data->mahaDashaPredictions_data, true) ?? null,
                    'd1Chart' => json_decode($kundali_pdf_data->d1Chart_data, true) ?? null,
                    'd9Chart' => json_decode($kundali_pdf_data->d9Chart_data, true) ?? null,
                ];
            }

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $responseData
            ], 200);

        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    private function getApiCallResponse($pool, $requestData, $api_key)
    {
        return [
            $pool->as('panchang')->get('https://api.vedicastroapi.com/v3-json/panchang/panchang', [
                'date' => $requestData['date'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),

            $pool->as('planetDetails')->get('https://api.vedicastroapi.com/v3-json/horoscope/planet-details', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),

            $pool->as('ascendantReport')->get('https://api.vedicastroapi.com/v3-json/horoscope/ascendant-report', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('planetReport')->get('https://api.vedicastroapi.com/v3-json/horoscope/planet-report', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'planet' => $requestData['planet'],
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('findMoonSign')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-moon-sign', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('findSunSign')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-sun-sign', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('findAcendant')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-ascendant', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('extendedKundliDetails')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/extended-kundli-details', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('gemSuggestion')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/gem-suggestion', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('numeroTable')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/numero-table', [
                'name' => $requestData['name'],
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('rudrakshSuggestion')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/rudraksh-suggestion', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('shadBala')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/shad-bala', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('friendshipTable')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/friendship', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('kpHouses')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-houses', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('kpPlanets')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-planets', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('mangalDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/mangal-dosh', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('KaalsarpDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/kaalsarp-dosh', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('managlikDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('pitraDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/pitra-dosh', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('papasamaya')->get('https://api.vedicastroapi.com/v3-json/dosha/papasamaya', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('currentSadeSati')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/current-sade-sati', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('sadeSatiTable')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/sade-sati-table', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('varshapalDetails')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-details', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('varshapalMonthChart')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-month-chart', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('varshapalYearChart')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-year-chart', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('yogaList')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/yoga-list', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('charDashaCurrent')->get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-current', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('charDashaMain')->get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-main', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('charDashaSub')->get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-sub', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('yoginiDashaMain')->get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-main', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('yoginiDashaSub')->get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-sub', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('specificDasha')->get('https://api.vedicastroapi.com/v3-json/dashas/specific-sub-dasha', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'md' => $requestData['mahadasha'],
                'ad' => $requestData['antardasha'],
                'pd' => $requestData['paryantardasha'],
                'sd' => $requestData['shookshamadasha'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('currentMahadashaFull')->get('https://api.vedicastroapi.com/v3-json/dashas/current-mahadasha-full', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            
            $pool->as('currentMahadasha')->get('https://api.vedicastroapi.com/v3-json/dashas/current-mahadasha', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),

            $pool->as('mahaDasha')->get('https://api.vedicastroapi.com/v3-json/dashas/maha-dasha', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),

            $pool->as('antarDasha')->get('https://api.vedicastroapi.com/v3-json/dashas/antar-dasha', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),

            $pool->as('paryantarDasha')->get('https://api.vedicastroapi.com/v3-json/dashas/paryantar-dasha', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),

            $pool->as('mahaDashaPredictions')->get('https://api.vedicastroapi.com/v3-json/dashas/maha-dasha-predictions', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
            ]),
            

            $pool->as('d1Chart')->get('https://api.vedicastroapi.com/v3-json/horoscope/divisional-charts', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'div' => $requestData['divChart'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
                'response_type' => $requestData['divChartResponseType'],
                'year' => $requestData['divChartYear'],
                'transit_date' => $requestData['divChartTransitDate'],
            ]),

            $pool->as('d9Chart')->get('https://api.vedicastroapi.com/v3-json/horoscope/chart-image', [
                'dob' => $requestData['birthDate'],
                'tob' => $requestData['birthTime'],
                'tz' => $requestData['timezone'],
                'lat' => $requestData['latitude'],
                'lon' => $requestData['longitude'],
                'div' => $requestData['divChart'],
                'api_key' => $api_key,
                'lang' => $requestData['language'],
                'color' => $requestData['chartImageColor'],
                'style' => $requestData['chartImageStyle'],
                'font_size' => $requestData['chartImageFontSize'],
                'font_style' => $requestData['chartImageFontStyle'],
                'size' => $requestData['chartImageSize'],
                'stroke' => $requestData['chartImageStroke'],
                'format' => $requestData['chartImageFormat'],
                'year' => $requestData['divChartYear'],
                'transit_date' => $requestData['divChartTransitDate'],
            ])
        ];
    }
    
}
