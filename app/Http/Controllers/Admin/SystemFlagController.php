<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\MstControl;

class SystemFlagController extends Controller
{
    public function getSystemFlag(Request $req)
    {
        try {
			
            if (Auth::guard('web')->check()) {
                $flagGroup = DB::table('flaggroup')->whereNull('parentFlagGroupId')->get();
                for ($i = 0; $i < count($flagGroup); $i++) {
                    $subGroup = DB::Table('flaggroup')->where('viewenable',1)->where('parentFlagGroupId', $flagGroup[$i]->id)->get();
                    if ($subGroup && count($subGroup) > 0) {
                        for ($j = 0; $j < count($subGroup); $j++) {
                            $systemFlag = $systemFlag = DB::table('systemflag')->where('isActive',1)->where('flagGroupId', $subGroup[$j]->id)->get();
                            $subGroup[$j]->systemFlag = $systemFlag;

                        }
                        $flagGroup[$i]->subGroup = $subGroup;
                        $systemFlag = DB::table('systemflag')->where('flagGroupId', $flagGroup[$i]->id)->get();
                        $flagGroup[$i]->systemFlag = $systemFlag;
                    } else {
                        $systemFlag = DB::table('systemflag')->where('flagGroupId', $flagGroup[$i]->id)->get();
                        $flagGroup[$i]->systemFlag = $systemFlag;
                        $flagGroup[$i]->subGroup = [];
                    }
                }
                $language = Language::query()->get();
                $mstData = MstControl::query()->get();
                $astroApiCallType = $mstData[0]->astro_api_call_type;
                return view('pages.system-flag', compact('flagGroup', 'language', 'astroApiCallType'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function editSystemFlag(Request $req)
    {

       
        try {
			//   return response()->json([
            //     'error' => ["This Option is disabled for Demo!"],
            // ]);
            
            if (Auth::guard('web')->check()) {
              //  MstControl::where('id', 1)->update(['astro_api_call_type' => trim($req->astroApiCallType)]);

                $flaggroups = $req->input('flaggroups');

                foreach ($flaggroups as $subGroupId => $data) {
                    $isActive = $data['isActive'] ?? 0; // Default to 0 if not present
            
                    DB::table('flaggroup')
                        ->where('id', '=', $data['id'])
                        ->update(['isActive' => $isActive]);
                }
                foreach ($req->group as $flag) {
                    if (array_key_exists('systemFlag', $flag) && count($flag['systemFlag']) > 0) {
                        foreach ($flag['systemFlag'] as $flagvalue) {
                            if (array_key_exists('value', $flagvalue)) {
                                if (array_key_exists('valueType', $flagvalue)) {
                                    if ($flagvalue['valueType'] == 'File') {
                                        $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                        $time = Carbon::now()->timestamp;
                                        $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                        $destinationpath = 'public/storage/images/';
                                        $imageName = $flagvalue['name'];
                                        $path = $destinationpath . $imageName . $time . '.png';
                                        File::delete($sysFile->value);
                                        file_put_contents($path, base64_decode($flagvalue['value']));
                                        $flagvalue['value'] = $path;
                                    }
                                    if ($flagvalue['valueType'] == 'MultiSelect') {
                                        $flagvalue['value'] = implode(',', $flagvalue['value']);
                                    }
                                    if ($flagvalue['valueType'] == 'Video') {
                                        $time = Carbon::now()->timestamp;
                                        $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                        $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                        $destinationpath = 'public/storage/images/';
                                        $imageName = $flagvalue['name'];
                                        $path = $destinationpath . $imageName . $time . '.mp4';
                                        File::delete($sysFile->value);
                                        file_put_contents($path, base64_decode($flagvalue['value']));
                                        $flagvalue['value'] = $path;
                                    }
                                }
                                $flagData = array(
                                    'value' => $flagvalue['value'],
                                );
                                DB::Table('systemflag')
                                    ->where('name', '=', $flagvalue['name'])
                                    ->update($flagData);
                            }
                        }
                    }
                    if (array_key_exists('subGroup', $flag) && count($flag['subGroup']) > 0) {
                        foreach ($flag['subGroup'] as $flagvalue) {
                            foreach ($flagvalue['systemFlag'] as $sys) {
                                if (array_key_exists('value', $sys)) {
                                    if (array_key_exists('valueType', $sys)) {
                                        if ($sys['valueType'] == 'File') {
                                            $sysFile = DB::Table('systemflag')->where('name', $sys['name'])->first();
                                            $time = Carbon::now()->timestamp;
                                            $sys['value'] = base64_encode(file_get_contents($sys['value']));
                                            $destinationpath = 'public/storage/images/';
                                            $imageName = $sys['name'];
                                            $path = $destinationpath . $imageName . $time . '.png';
                                            File::delete($sysFile->value);
                                            file_put_contents($path, base64_decode($sys['value']));
                                            $sys['value'] = $path;
                                        }
                                        if ($sys['valueType'] == 'MultiSelect') {
                                            $sys['value'] = implode(',', $sys['value']);
                                        }
                                        if ($sys['valueType'] == 'Video') {
                                            $sysFile = DB::Table('systemflag')->where('name', $sys['name'])->first();
                                            $time = Carbon::now()->timestamp;
                                            $sys['value'] = base64_encode(file_get_contents($sys['value']));
                                            $destinationpath = 'public/storage/images/';
                                            $imageName = $sys['name'];
                                            $path = $destinationpath . $imageName . $time . '.mp4';
                                            File::delete($sysFile->value);
                                            file_put_contents($path, base64_decode($sys['value']));
                                            $sys['value'] = $path;
                                        }
                                    }
                                    $flagData = array(
                                        'value' => $sys['value'],
                                    );
                                    DB::Table('systemflag')
                                        ->where('name', '=', $sys['name'])
                                        ->update($flagData);
                                }
                            }
                        }
                    }
                }

                return response()->json([
                    'success' => "SystemFlag Update",
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }
}
