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

            $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

            $maleKundliId = $req->male_kundli_id;
            $femaleKundliId = $req->female_kundli_id;
            $maleRcd = Kundali::where('id', $maleKundliId)->first();
            $femaleRcd = Kundali::where('id', $femaleKundliId)->first();
            $girlMangalikRpt = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                'dob' => date('d/m/Y',strtotime($maleRcd->birthDate)),
                'tob' => $maleRcd->birthTime,
                'tz' => $maleRcd->timezone,
                'lat' => $maleRcd->latitude,
                'lon' => $maleRcd->longitude,
                'api_key' => $api_key->value,
                'lang' => 'en'
            ]);
            $boyManaglikRpt = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                'dob' => date('d/m/Y',strtotime($femaleRcd->birthDate)),
                'tob' => $femaleRcd->birthTime,
                'tz' => $femaleRcd->timezone,
                'lat' => $femaleRcd->latitude,
                'lon' => $femaleRcd->longitude,
                'api_key' => $api_key->value,
                'lang' => 'en'
            ]);
            if(strtolower($femaleRcd->match_type) == strtolower('North')){
                //
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
            }else{
                //
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
            }
            $data = $dailyHorscope->json();

            return response()->json([
                'message' => 'Boys and girls matching details fetched sucessfully',
                'recordList' => $data,
                'girlMangalikRpt' => $girlMangalikRpt->json(),
                'boyManaglikRpt' => $boyManaglikRpt->json(),
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
