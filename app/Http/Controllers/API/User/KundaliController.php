<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Kundali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class KundaliController extends Controller
{

    public function addKundali(Request $req)
    {

        try {
            // Get user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $isMatchString = $req->is_match;
            $isMatchBoolean = $isMatchString === 'true';
            // dd($id);
            $data = $req->only('kundali', 'amount', 'is_match');

            // Validate the data
            $validator = Validator::make($data, [
                'kundali' => 'required|array',
                'amount' => 'required|numeric', // Assuming amount is required and should be numeric
            ]);

            // Send a failed response if the request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $kundali2 = [];

            // Create or update Kundali
            foreach ($req->kundali as $kundali) {

                if (isset($kundali['id'])) {
                    $kundalis = Kundali::find($kundali['id']);

                    if ($kundalis) {
                        if($req->is_pdf_generate){
                            $kundaliList = $this->getKundliViaVedic(
                                $kundali['lang'],
                                $kundali['name'],
                                $kundali['latitude'],
                                $kundali['longitude'],
                                $kundali['birthDate'],
                                $kundali['birthTime'],
                                $kundali['timezone'],
                                $kundali['birthPlace']
                            );
                        }
                        else{
                            $kundaliList = null;
                        }

                        $kundalis->name = $kundali['name'];
                        $kundalis->gender = $kundali['gender'];
                        $kundalis->birthDate = date('Y-m-d', strtotime($kundali['birthDate']));
                        $kundalis->birthTime = $kundali['birthTime'];
                        $kundalis->birthPlace = $kundali['birthPlace'];
                        $kundalis->latitude = $kundali['latitude'];
                        $kundalis->longitude = $kundali['longitude'];
                        $kundalis->timezone = $kundali['timezone'];
                        $kundalis->pdf_type = isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '';
                        $kundalis->match_type = isset($kundali['match_type']) ? $kundali['match_type'] : '';
                        $kundalis->forMatch = isset($kundali['forMatch']) ? $kundali['forMatch'] : 0;
                        $kundalis->pdf_link = isset($kundaliList) ? $kundaliList : '';
                        $kundalis->update();
                        $kundali2[] = $kundalis;
                    }
                } else {

                    // Check if wallet has enough amount only if is_match is false
                    $kundalicount = Kundali::where('createdBy', '=', $id)->count();
                    if (!$isMatchBoolean && $kundalicount>0) {

                        $wallet = DB::table('user_wallets')
                            ->where('userId', '=', $id)
                            ->first();

                        $requiredAmount = $req->amount;

                        if ($wallet && $wallet->amount >= $requiredAmount) {

                            $updatedAmount = $wallet->amount - $requiredAmount;

                            DB::table('user_wallets')
                                ->where('userId', $id)
                                ->update(['amount' => $updatedAmount]);
                                if($req->is_pdf_generate){
                                    $kundaliList = $this->getKundliViaVedic(
                                        $kundali['lang'],
                                        $kundali['name'],
                                        $kundali['latitude'],
                                        $kundali['longitude'],
                                        $kundali['birthDate'],
                                        $kundali['birthTime'],
                                        $kundali['timezone'],
                                        $kundali['birthPlace']
                                    );
                                }
                                else{
                                    $kundaliList = "";
                                }



                            $newKundali = Kundali::create([
                                'name' => $kundali['name'],
                                'gender' => $kundali['gender'],
                                'birthDate' => date('Y-m-d', strtotime($kundali['birthDate'])),
                                'birthTime' => $kundali['birthTime'],
                                'birthPlace' => $kundali['birthPlace'],
                                'createdBy' => $id,
                                'modifiedBy' => $id,
                                'latitude' => $kundali['latitude'],
                                'longitude' => $kundali['longitude'],
                                'timezone' => $kundali['timezone'],
                                'pdf_type' => isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '',
                                'match_type' => isset($kundali['match_type']) ? $kundali['match_type'] : '',
                                'forMatch' => isset($kundali['forMatch']) ? $kundali['forMatch'] : 0,
                                'pdf_link' => isset($kundaliList) ? $kundaliList : '',

                            ]);

                            $kundali2[] = $newKundali;

                            // Add wallet transaction entry
                            $transaction = [
                                'userId' => $id,
                                'amount' => $requiredAmount,
                                'isCredit' => false,
                                'transactionType' => 'KundliView',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            DB::table('wallettransaction')->insert($transaction);
                        } else {
                            // Insufficient funds in the wallet
                            return response()->json([
                                'error' => true,
                                'message' => 'Insufficient funds in the wallet.',
                                'status' => 400,
                            ], 400);
                        }
                    } else {
                        // dd( $req->forMatch);
                            // dd( $kundali);


                        // If is_match is true, don't perform wallet-related actions
                        $newKundali = Kundali::create([
                            'name' => $kundali['name'],
                            'gender' => $kundali['gender'],
                            'birthDate' => date('Y-m-d', strtotime($kundali['birthDate'])),
                            'birthTime' => $kundali['birthTime'],
                            'birthPlace' => $kundali['birthPlace'],
                            'createdBy' => $id,
                            'modifiedBy' => $id,
                            'latitude' => $kundali['latitude'],
                            'longitude' => $kundali['longitude'],
                            'timezone' => $kundali['timezone'],
                            'pdf_type' => isset($kundali['pdf_type']) ? $kundali['pdf_type'] : '',
                            'match_type' => isset($kundali['match_type']) ? $kundali['match_type'] : '',
                            'forMatch' => isset($kundali['forMatch']) ? $kundali['forMatch'] : 0,
                            'pdf_link' => isset($kundaliList) ? $kundaliList : '',
                        ]);

                        $kundali2[] = $newKundali;
                    }
                }
            }

            return response()->json([
                'message' => 'Kundali updated successfully',
                'recordList' => $kundali2,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getPanchang(Request $req)
    {
        $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

        // dd($api_key);

        try {
            $curl = curl_init();
            $date = date('d/m/Y',strtotime($req->panchangDate));
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.vedicastroapi.com/v3-json/panchang/panchang?api_key='.$api_key->value.'&date='.$date.'&tz=5.5&lat=11.2&lon=77.00&time=05%3A20&lang=en',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return response()->json([
                'recordList' => json_decode($response),
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

	public function getKundaliPrice(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $kundali = Kundali::where('createdBy', '=', $id)->count();
            return response()->json([
                'recordList' => config('constants.PDF_PRICE'),
                'isFreeSession' => $kundali > 0 ? false : true,
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

//   public function getKundliViaVedic($name,$lat, $long, $dob, $tob, $timezone, $pob)
    // {
	// 	$curl = curl_init();
	// 	curl_setopt_array($curl, array(
	// 	  CURLOPT_URL => 'https://api.vedicastroapi.com/v3-json/pdf/horoscope?name=Karan%20Test&dob=03%2F01%2F1996&tob=09%3A04&lat=28.7040592&lon=77.1024902&tz=5.5&api_key=2d0cefb1-b103-5a55-8f0b-499862d7d62c&lang=en&style=north&color=140&pob=Coimbatore&company_name=AstroWay&address=Delhi&website=https%3A%2F%2Fastroway.diploy.in%2F&email=nb%40diploy.in&phone=%2B91%208690482938&pdf_type=medium',
	// 	  CURLOPT_RETURNTRANSFER => true,
	// 	  CURLOPT_ENCODING => '',
	// 	  CURLOPT_MAXREDIRS => 10,
	// 	  CURLOPT_TIMEOUT => 0,
	// 	  CURLOPT_FOLLOWLOCATION => true,
	// 	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 	  CURLOPT_CUSTOMREQUEST => 'GET',
	// 	));
	// 	$response = curl_exec($curl);
	// 	return $response;
    // }

  //Dynamic part
    public function getKundliViaVedic($lang,$name, $lat, $long, $dob, $tob, $timezone, $pob, $pdfType = 'small', $match_type = 'north')
        {
		 $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

        $formattedBirthDate = date('d/m/Y', strtotime($dob));
        $apiUrl = 'https://api.vedicastroapi.com/v3-json/pdf/horoscope?';

        $queryParams = http_build_query([
            'name' => $name,
            'dob' => $formattedBirthDate,
            'tob' => $tob,
            'lat' => $lat,
            'lon' => $long,
            'tz' => $timezone,
            'pob' => $pob,
            'api_key' => $api_key->value,
            'lang' => $lang,
            'style' => $match_type,
            'color' => '140',
            'pdf_type' => $pdfType,
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl . $queryParams,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);

        // Check if the request was successful
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            $response = json_decode($response);


            $timestamp = now()->timestamp;
            $path = 'kundli/' . $name . '_kundali_' . $timestamp . '.pdf';

            // Save the PDF to a local file
            $pdfPath = public_path($path);

            $content = file_get_contents($response->response);
            file_put_contents($pdfPath, $content);

            // Close the cURL session
            curl_close($curl);

            // Return the local path to the saved PDF
            return $path;
        } else {
            // Handle error (e.g., log or return an error message)
            curl_close($curl);
            return false;
        }
    }

    //Get kundali
    public function getKundalis(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $kundali = Kundali::query();
            $kundali->where('createdBy', '=', $id)->where('forMatch',0)->orderByDesc('created_at');
            $kundaliCount = Kundali::query();
            $kundaliCount->where('createdBy', '=', $id)
                ->where('forMatch',0)->count();
            if ($s = $req->input(key:'s')) {
                $kundali->whereRaw(sql:"name LIKE '%" . $s . "%' ");
            }

            return response()->json([
                'recordList' => $kundali->get(),
                'status' => 200,
                'totalRecords' => $id,
				'kundliList' => json_decode('{"status":200,"response":"https://s3.ap-south-1.amazonaws.com/vapi.public.pdf/Tue%20Jan%2009%202024/hor_Karan%20Test-03011996-0904-1704796707150.pdf?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAVSWEL6DIXT6LAQVK%2F20240109%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20240109T103827Z&X-Amz-Expires=21600&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEFMaCmFwLXNvdXRoLTEiSDBGAiEAriZ4vzJ0CAA2H0N29ZMW1neGqFa1IdcDyOCHnPh5%2FE8CIQDJja9Kos0jIqMPoJs5WUmimBnymLdPx4zmpsjPp5BdSCr4Agjs%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8BEAQaDDM4MzczNzY1NTUwNSIMWoYe92QqOOym96aCKswCGx7RjJjuAPolXNSBpB2XNNTFQESlMDoA7R4uQHhiLNMbk7BllB9j3Gz5ajQAPnIoiyyDEhaN9XFVLayAeU%2F8i%2Bk8LLrwwrv16NZ%2F4DR%2BTjkfrViKbKyNUXaJpRMT4t8iWP5%2FKEdkpVNfAjCoVvXFX3Nq1nE%2BBI2jf2AIPjgfXRjinYLuPVsErK2mMxk0V2C8wl5%2BPAkPlSsKuTbo1vvnGNd6Ny0mKsnA8U642CJUvaxKGIDSHAiNn7jYTcLsN9Un%2FOtQntNRNmGrRbEa3SJvVZLIgVqpTsOusvRLNIOCVpE5wQX3JpoOPWYr302nA%2FQ0zj4j9%2F4hmxzMJWDbZVlzNOIwxNdRlCbh%2FtcOAi9Sg00SPLxUFB1FzPz9hHphfVIoZwWy5vEJ1fVXx%2BpCwaCNom%2Bltyccr%2FL915Yrto8oHhoKl3YeFaqJNlvEWx0wiML0rAY6nQEB0myq%2B%2FG9KzhzoGh9t9NGpbr8bfzgcj273Ru6sn8CzATeYOIKSK8Lusd9KVv7s2VvwRMmlcenuRSOIJEMObOxPUqaO2hG9SjnpCbu8DMShd%2BUoHo505%2BEm9K520gEA5cvhVieGHwlFxk4BbSN4bh8A2b7F4j17G9Stp1q6XrMGmLcY3RVmMYdRfjQ2u%2BQu2hr%2FiSu9olOUXtLyDg0&X-Amz-Signature=d50e3e354b5c1cfab0953c1eaf088a750b804af4d03185e63060e9a169b6cddc&X-Amz-SignedHeaders=host"}'),//$kundaliList,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


	// 	public function getKundali(Request $req, $id)
    // {
    //     try {
    //         $kundali = Kundali::where('id', $id)->first();
    //         $dob = date('d/m/Y', strtotime($kundali->birthDate));
    //         return response()->json([
    //             'message' => 'Kundali update sucessfully',
    //             'recordList' => json_decode('{"status":200,"response":"https://astroway.diploy.in/public/hor_Karan%20Test-03011996-0904-1704796707150.pdf"}'),
    //             'status' => 200,
    //         ], 200);
    //     } catch (\Exception$e) {
    //         return response()->json([
    //             'error' => false,
    //             'message' => $e->getMessage(),
    //             'status' => 500,
    //         ], 500);
    //     }
    // }

	//dynamic part
	public function getKundali(Request $req, $id)
    {
        try {
            $kundali = Kundali::where('id', $id)->first();
            if(!$kundali){
                return response()->json([
                    'message' => 'Kundali not found',
                    'status' => 404,
                ], 404);
            }
            $dob = date('d/m/Y', strtotime($kundali->birthDate));
            return response()->json([
                'message' => 'Kundali get sucessfully',
                'recordList' => ['status'=>200,'response'=>url('public/'.$kundali->pdf_link)],
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




    //Update kundali
    public function updateKundali(Request $req, $id)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $req->validate = ([
                'name',
                'gender',
                'birthDate',
                'birthTime',
                'birthPlace',
            ]);

            $kundali = Kundali::find($id);
            if ($kundali) {
                $kundali->name = $req->name;
                $kundali->gender = $req->gender;
                $kundali->birthDate = $req->birthDate;
                $kundali->birthTime = $req->birthTime;
                $kundali->birthPlace = $req->birthPlace;
                $kundali->latitude = $req->latitude;
                $kundali->longitude = $req->longitude;
                $kundali->timezone = $req->timezone;
                $kundali->update();
                return response()->json([
                    'message' => 'Kundali update sucessfully',
                    'recordList' => $kundali,
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Delete kundali
    public function deleteKundali(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $kundali = Kundali::find($req->id);
            if ($kundali) {

                $path = 'public'.'/'.$kundali->pdf_link;


                if (File::exists($path)) {
                    File::delete($path);
                }

                $kundali->delete();
                return response()->json([
                    'message' => 'Kundali delete Sucessfully',
                    'status' => 200,
                ], 200);
            }
            else {
                return response()->json([
                    'message' => 'Kundali not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show single kundali
    public function kundaliShow($id)
    {
        try {
            $kundali = Kundali::find($id);
            if ($kundali) {
                return response()->json([
                    'recordList' => $kundali,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Kundali is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function removeFromTrackPlanet(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = array(
                'isForTrackPlanet' => false,
            );
            DB::table('kundalis')->where('createdBy', '=', $id)->where('isForTrackPlanet', '=', true)->update($data);
            return response()->json([
                'message' => "Remove Kundali Successfully",
                'status' => 200,
                "id" => $id,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addForTrackPlanet(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = array(
                'isForTrackPlanet' => true,
            );
            DB::table('kundalis')->where('id', '=', $req->id)->update($data);
            return response()->json([
                'message' => "Kundali Add Successfully",
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

    public function getForTrackPlanet(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $trackPlanetKundali = DB::table('kundalis')->where('createdBy', '=', $id)->where('isForTrackPlanet', '=', true)->get();

            return response()->json([
                'recordList' => $trackPlanetKundali,
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
