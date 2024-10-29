<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\KundaliMatching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModel\Kundali;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KundaliMatchingController extends Controller
{
    //Add a kundali boy and girls
    public function addKundaliMatching(Request $req)
    {
        try {
            //Get a id of user
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'boyName',
                'boyBirthDate',
                'boyBirthTime',
                'boyBirthPlace',
                'girlName',
                'girlBirthDate',
                'girlBirthTime',
                'girlBirthPlace',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'boyName' => 'required',
                'boyBirthDate' => 'required',
                'boyBirthTime' => 'required',
                'boyBirthPlace' => 'required',
                'girlName' => 'required',
                'girlBirthDate' => 'required',
                'girlBirthTime' => 'required',
                'girlBirthPlace' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create kundali
            $kundaliMatching = KundaliMatching::create([
                'boyName' => $req->boyName,
                'boyBirthDate' => $req->boyBirthDate,
                'boyBirthTime' => $req->boyBirthTime,
                'boyBirthPlace' => $req->boyBirthPlace,
                'girlName' => $req->girlName,
                'girlBirthDate' => $req->girlBirthDate,
                'girlBirthTime' => $req->girlBirthTime,
                'girlBirthPlace' => $req->girlBirthPlace,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            return response()->json([
                'message' => 'Boys and girls details add sucessfully',
                'recordList' => $kundaliMatching,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getMatchReport(Request $req)
    {
        try {
            $data = $req->only(
                'male_kundli_id',
                'female_kundli_id'
            );

            $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->select('value')->first();

            if(!$api_key){
                return response()->json([
                    'error' => false,
                    'message' => "Invalid Vedic Astro API",
                    'status' => 400,
                ], 400);
            }

            $maleKundliId = $req->male_kundli_id;
            $femaleKundliId = $req->female_kundli_id;
            $maleRcd = Kundali::where('id', $maleKundliId)
            ->select(
                'birthDate',
                'birthTime',
                'timezone',
                'latitude',
                'longitude',
            )
            ->first();

            $femaleRcd = Kundali::where('id', $femaleKundliId)
            ->select(
                'birthDate',
                'birthTime',
                'timezone',
                'latitude',
                'longitude',
            )
            ->first();

            $horoscopeData = [];
            $girlManaglikReportData = [];
            $boyManaglikReportData = [];

            $girlMangliskDoshData = DB::table('kundali_matching_report_data')
                ->where('male_kundli_id',$maleKundliId)
                ->where('boy_birthDate',$maleRcd->birthDate)
                ->where('boy_birthTime',$maleRcd->birthTime)
                ->where('boy_timezone',$maleRcd->timezone)
                ->where('boy_latitude',$maleRcd->latitude)
                ->where('boy_longitude',$maleRcd->longitude)
                ->whereNotNull('girl_manglik_report_data')
                ->select('girl_manglik_report_data')
                ->orderByDesc('id')
                ->first();
            if($girlMangliskDoshData){
                $girlManaglikReportData = json_decode($girlMangliskDoshData->girl_manglik_report_data,true) ?? [];
            }
            else {
                $girlMangalikRpt = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                    'dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                    'tob' => $maleRcd->birthTime,
                    'tz' => $maleRcd->timezone,
                    'lat' => $maleRcd->latitude,
                    'lon' => $maleRcd->longitude,
                    'api_key' => $api_key->value,
                    'lang' => 'en'
                ]);

                DB::table('kundali_matching_report_data')
                ->insert([
                    'male_kundli_id' => $maleKundliId,
                    'female_kundli_id' => $femaleKundliId,
                    'boy_birthDate' => $maleRcd->birthDate,
                    'boy_birthTime' => $maleRcd->birthTime,
                    'boy_timezone' => $maleRcd->timezone,
                    'boy_latitude' => $maleRcd->latitude,
                    'boy_longitude' => $maleRcd->longitude,
                    'girl_manglik_report_data' => json_encode($girlMangalikRpt->json()),
                ]);

                $girlManaglikReportData = $girlMangalikRpt->json();
            }


            $boyMangliskDoshData = DB::table('kundali_matching_report_data')
                ->where('female_kundli_id',$femaleKundliId)
                ->where('girl_birthDate',$femaleRcd->birthDate)
                ->where('girl_birthTime',$femaleRcd->birthTime)
                ->where('girl_timezone',$femaleRcd->timezone)
                ->where('girl_latitude',$femaleRcd->latitude)
                ->where('girl_longitude',$femaleRcd->longitude)
                ->whereNotNull('boy_manglik_report_data')
                ->select('boy_manglik_report_data')
                ->orderByDesc('id')
                ->first();

            if($boyMangliskDoshData){
                $boyManaglikReportData = json_decode($boyMangliskDoshData->boy_manglik_report_data,true) ?? [];
            }
            else {
                $boyManaglikRpt = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                    'dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                    'tob' => $femaleRcd->birthTime,
                    'tz' => $femaleRcd->timezone,
                    'lat' => $femaleRcd->latitude,
                    'lon' => $femaleRcd->longitude,
                    'api_key' => $api_key->value,
                    'lang' => 'en'
                ]);

                DB::table('kundali_matching_report_data')
                ->insert([
                    'male_kundli_id' => $maleKundliId,
                    'female_kundli_id' => $femaleKundliId,
                    'girl_birthDate' => $femaleRcd->birthDate,
                    'girl_birthTime' => $femaleRcd->birthTime,
                    'girl_timezone' => $femaleRcd->timezone,
                    'girl_latitude' => $femaleRcd->latitude,
                    'girl_longitude' => $femaleRcd->longitude,
                    'boy_manglik_report_data' => json_encode($boyManaglikRpt->json()),
                ]);

                $boyManaglikReportData = $boyManaglikRpt->json();
            }

            if(strtolower($femaleRcd->match_type) == strtolower('North')){
                $ashtakootData = DB::table('kundali_matching_report_data')
                    ->where('male_kundli_id',$maleKundliId)
                    ->where('female_kundli_id',$femaleKundliId)
                    ->where('boy_birthDate',$maleRcd->birthDate)
                    ->where('boy_birthTime',$maleRcd->birthTime)
                    ->where('boy_timezone',$maleRcd->timezone)
                    ->where('boy_latitude',$maleRcd->latitude)
                    ->where('boy_longitude',$maleRcd->longitude)
                    ->where('girl_birthDate',$femaleRcd->birthDate)
                    ->where('girl_birthTime',$femaleRcd->birthTime)
                    ->where('girl_timezone',$femaleRcd->timezone)
                    ->where('girl_latitude',$femaleRcd->latitude)
                    ->where('girl_longitude',$femaleRcd->longitude)
                    ->whereNotNull('ashtakoot_horoscope_data')
                    ->select('ashtakoot_horoscope_data')
                    ->orderByDesc('id')
                    ->first();

                if($ashtakootData){
                    $horoscopeData = json_decode($ashtakootData->ashtakoot_horoscope_data,true) ?? [];
                }
                else {
                    $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/matching/ashtakoot', [
                        'boy_dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                        'boy_tob' => $maleRcd->birthTime,
                        'boy_tz' => $maleRcd->timezone,
                        'boy_lat' => $maleRcd->latitude,
                        'boy_lon' => $maleRcd->longitude,
                        'girl_dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                        'girl_tob' => $femaleRcd->birthTime,
                        'girl_tz' => $femaleRcd->timezone,
                        'girl_lat' => $femaleRcd->latitude,
                        'girl_lon' => $femaleRcd->longitude,
                        'api_key' => $api_key->value,
                        'lang' => 'en'
                    ]);

                    DB::table('kundali_matching_report_data')
                    ->insert([
                        'male_kundli_id' => $maleKundliId,
                        'female_kundli_id' => $femaleKundliId,
                        'girl_birthDate' => $femaleRcd->birthDate,
                        'girl_birthTime' => $femaleRcd->birthTime,
                        'girl_timezone' => $femaleRcd->timezone,
                        'girl_latitude' => $femaleRcd->latitude,
                        'girl_longitude' => $femaleRcd->longitude,
                        'boy_birthDate' => $maleRcd->birthDate,
                        'boy_birthTime' => $maleRcd->birthTime,
                        'boy_timezone' => $maleRcd->timezone,
                        'boy_latitude' => $maleRcd->latitude,
                        'boy_longitude' => $maleRcd->longitude,
                        'ashtakoot_horoscope_data' => json_encode($dailyHorscope->json()),
                    ]);

                    $horoscopeData = $dailyHorscope->json();
                }

            }else{
                $dashakootData = DB::table('kundali_matching_report_data')
                    ->where('male_kundli_id',$maleKundliId)
                    ->where('female_kundli_id',$femaleKundliId)
                    ->where('boy_birthDate',$maleRcd->birthDate)
                    ->where('boy_birthTime',$maleRcd->birthTime)
                    ->where('boy_timezone',$maleRcd->timezone)
                    ->where('boy_latitude',$maleRcd->latitude)
                    ->where('boy_longitude',$maleRcd->longitude)
                    ->where('girl_birthDate',$femaleRcd->birthDate)
                    ->where('girl_birthTime',$femaleRcd->birthTime)
                    ->where('girl_timezone',$femaleRcd->timezone)
                    ->where('girl_latitude',$femaleRcd->latitude)
                    ->where('girl_longitude',$femaleRcd->longitude)
                    ->whereNotNull('dashakoot_horoscope_data')
                    ->select('dashakoot_horoscope_data')
                    ->orderByDesc('id')
                    ->first();

                if($dashakootData){
                    $horoscopeData = json_decode($dashakootData->dashakoot_horoscope_data,true) ?? [];
                }
                else {
                    $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/matching/dashakoot', [
                        'boy_dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                        'boy_tob' => $maleRcd->birthTime,
                        'boy_tz' => $maleRcd->timezone,
                        'boy_lat' => $maleRcd->latitude,
                        'boy_lon' => $maleRcd->longitude,
                        'girl_dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                        'girl_tob' => $femaleRcd->birthTime,
                        'girl_tz' => $femaleRcd->timezone,
                        'girl_lat' => $femaleRcd->latitude,
                        'girl_lon' => $femaleRcd->longitude,
                        'api_key' => $api_key->value,
                        'lang' => 'en'
                    ]);

                    DB::table('kundali_matching_report_data')
                    ->insert([
                        'male_kundli_id' => $maleKundliId,
                        'female_kundli_id' => $femaleKundliId,
                        'girl_birthDate' => $femaleRcd->birthDate,
                        'girl_birthTime' => $femaleRcd->birthTime,
                        'girl_timezone' => $femaleRcd->timezone,
                        'girl_latitude' => $femaleRcd->latitude,
                        'girl_longitude' => $femaleRcd->longitude,
                        'boy_birthDate' => $maleRcd->birthDate,
                        'boy_birthTime' => $maleRcd->birthTime,
                        'boy_timezone' => $maleRcd->timezone,
                        'boy_latitude' => $maleRcd->latitude,
                        'boy_longitude' => $maleRcd->longitude,
                        'dashakoot_horoscope_data' => json_encode($dailyHorscope->json()),
                    ]);

                    $horoscopeData = $dailyHorscope->json();
                }
            }

            return response()->json([
                'message' => 'Boys and girls matching details fetched sucessfully',
                'recordList' => $horoscopeData,
                'girlMangalikRpt' => $girlManaglikReportData,
                'boyManaglikRpt' => $boyManaglikReportData,

                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
