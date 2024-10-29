<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    //Get a language
    public function getLanguages()
    {
        try {
            // $recordList = json_decode(File::get(public_path('country.json')), true);

            // return response()->json([
            //     'recordList' => [
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList, 
            //         ...$recordList
            //     ],
            //     'status' => 200,
            // ],200);

            // $language = Language::all();
            $language = DB::table('languages')
            ->select('id','languageName','languageCode','language_sign')
            ->get();
            
            return response()->json([
                'recordList' => $language,
                'status' => 200,
            ],200);
        } catch (\Exception$e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ],500);
        }
    }
}
