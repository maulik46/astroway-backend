<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemFlagController extends Controller
{
    public function getSystemFlag(Request $req)
    {
        try {
            $systemFlag = DB::table('systemflag')
                ->get();
            return response()->json([
                'recordList' => $systemFlag,
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

    public function getSubCategory(Request $req)
    {
        try {
            $systemFlag = DB::table('systemflag')->join('sub_category', 'sub_category.parent_id', '=', 'systemflag.id')->where('name', 'Category')->get();
            return response()->json([
                'recordList' => $systemFlag,
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
  
    public function getAppLanguage(Request $req)
    {
        try {
            $appLanguage = DB::table('systemflag')->where('name', 'Language')->get();
            $appLanguage = array_map('intval', explode(',', $appLanguage[0]->value));
            $language = DB::table('languages')
                ->whereIn('id', $appLanguage)
                ->get();
            return response()->json([
                'status' => 200,
                'message' => 'Get Language Successfully',
                'recordList' => $language,
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
