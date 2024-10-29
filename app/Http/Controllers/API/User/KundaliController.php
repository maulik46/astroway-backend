<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Kundali;
use App\Models\UserModel\KundaliPdfData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

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
                        if ($req->is_pdf_generate) {
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
                        } else {
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
                    if (!$isMatchBoolean && $kundalicount > 0) {

                        $wallet = DB::table('user_wallets')
                            ->where('userId', '=', $id)
                            ->first();

                        $requiredAmount = $req->amount;

                        if ($wallet && $wallet->amount >= $requiredAmount) {

                            $updatedAmount = $wallet->amount - $requiredAmount;

                            DB::table('user_wallets')
                                ->where('userId', $id)
                                ->update(['amount' => $updatedAmount]);
                            if ($req->is_pdf_generate) {
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
                            } else {
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
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->select('value')->first();

        try {
            $getPanchang = DB::table('datewise_panchang')->where('date', date('Y-m-d', strtotime($req->panchangDate)))->select('panchang')->first();

            if ($getPanchang) {
                return response()->json([
                    'recordList' => json_decode($getPanchang->panchang, true) ?? [],
                    'status' => 200,
                ], 200);
            } else {
                $curl = curl_init();
                $date = date('d/m/Y', strtotime($req->panchangDate));
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.vedicastroapi.com/v3-json/panchang/panchang?api_key=' . $api_key->value . '&date=' . $date . '&tz=5.5&lat=11.2&lon=77.00&time=05%3A20&lang=en',
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

                DB::table('datewise_panchang')->insert([
                    'date' => date('Y-m-d', strtotime($req->panchangDate)),
                    'panchang' => $response,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d')
                ]);

                return response()->json([
                    'recordList' => json_decode($response),
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Dynamic part
    public function getKundliViaVedic($lang, $name, $lat, $long, $dob, $tob, $timezone, $pob, $pdfType = 'small', $match_type = 'north')
    {
        $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->select('value')->first();

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
            $kundali->where('createdBy', '=', $id)->where('forMatch', 0)->orderByDesc('created_at');
            $kundaliCount = Kundali::query();
            $kundaliCount->where('createdBy', '=', $id)->where('forMatch', 0)->count();
            if ($s = $req->input(key: 's')) {
                $kundali->whereRaw(sql: "name LIKE '%" . $s . "%' ");
            }

            return response()->json([
                'recordList' => $kundali->get(),
                'status' => 200,
                'totalRecords' => $id,
                'kundliList' => json_decode('{"status":200,"response":"https://s3.ap-south-1.amazonaws.com/vapi.public.pdf/Tue%20Jan%2009%202024/hor_Karan%20Test-03011996-0904-1704796707150.pdf?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=ASIAVSWEL6DIXT6LAQVK%2F20240109%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20240109T103827Z&X-Amz-Expires=21600&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEFMaCmFwLXNvdXRoLTEiSDBGAiEAriZ4vzJ0CAA2H0N29ZMW1neGqFa1IdcDyOCHnPh5%2FE8CIQDJja9Kos0jIqMPoJs5WUmimBnymLdPx4zmpsjPp5BdSCr4Agjs%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8BEAQaDDM4MzczNzY1NTUwNSIMWoYe92QqOOym96aCKswCGx7RjJjuAPolXNSBpB2XNNTFQESlMDoA7R4uQHhiLNMbk7BllB9j3Gz5ajQAPnIoiyyDEhaN9XFVLayAeU%2F8i%2Bk8LLrwwrv16NZ%2F4DR%2BTjkfrViKbKyNUXaJpRMT4t8iWP5%2FKEdkpVNfAjCoVvXFX3Nq1nE%2BBI2jf2AIPjgfXRjinYLuPVsErK2mMxk0V2C8wl5%2BPAkPlSsKuTbo1vvnGNd6Ny0mKsnA8U642CJUvaxKGIDSHAiNn7jYTcLsN9Un%2FOtQntNRNmGrRbEa3SJvVZLIgVqpTsOusvRLNIOCVpE5wQX3JpoOPWYr302nA%2FQ0zj4j9%2F4hmxzMJWDbZVlzNOIwxNdRlCbh%2FtcOAi9Sg00SPLxUFB1FzPz9hHphfVIoZwWy5vEJ1fVXx%2BpCwaCNom%2Bltyccr%2FL915Yrto8oHhoKl3YeFaqJNlvEWx0wiML0rAY6nQEB0myq%2B%2FG9KzhzoGh9t9NGpbr8bfzgcj273Ru6sn8CzATeYOIKSK8Lusd9KVv7s2VvwRMmlcenuRSOIJEMObOxPUqaO2hG9SjnpCbu8DMShd%2BUoHo505%2BEm9K520gEA5cvhVieGHwlFxk4BbSN4bh8A2b7F4j17G9Stp1q6XrMGmLcY3RVmMYdRfjQ2u%2BQu2hr%2FiSu9olOUXtLyDg0&X-Amz-Signature=d50e3e354b5c1cfab0953c1eaf088a750b804af4d03185e63060e9a169b6cddc&X-Amz-SignedHeaders=host"}'), //$kundaliList,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    //dynamic part
    public function getKundali(Request $req, $id)
    {
        try {
            $kundali = Kundali::where('id', $id)->select('birthDate', 'pdf_link')->first();
            if (!$kundali) {
                return response()->json([
                    'message' => 'Kundali not found',
                    'status' => 404,
                ], 404);
            }
            $dob = date('d/m/Y', strtotime($kundali->birthDate));
            return response()->json([
                'message' => 'Kundali get sucessfully',
                'recordList' => ['status' => 200, 'response' => url('public/' . $kundali->pdf_link)],
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
        } catch (\Exception $e) {
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

                $path = 'public' . '/' . $kundali->pdf_link;


                if (File::exists($path)) {
                    File::delete($path);
                }

                $kundali->delete();
                return response()->json([
                    'message' => 'Kundali delete Sucessfully',
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Kundali not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getkundaliPdfdata(Request $req)
    {
        try {
            $api_key = DB::table('systemflag')->where('name', 'vedicAstroAPI')->select('value')->first();
            if (!$api_key) {
                return response()->json([
                    'error' => false,
                    'message' => "Invalid Vedic Astro API",
                    'status' => 400,
                ], 400);
            }
            $api_key = $api_key->value;
            $kundaliId = $req->kundali_id;
            $name = $req->name;
            $date = date('d/m/Y', strtotime($req->date));
            $birthDate = date('d/m/Y', strtotime($req->birthDate));
            $birthTime = $req->birthTime;
            $timezone = $req->timezone;
            $latitude = $req->latitude;
            $longitude = $req->longitude;
            $planet = $req->planet;
            $language = $req->language;
            $mahadasha = $req->mahadasha;
            $antardasha = $req->antardasha;
            $paryantardasha = $req->paryantardasha;
            $shookshamadasha = $req->shookshamadasha;

            $kundali_pdf_data = KundaliPdfData::where('kundali_id', $kundaliId)
                ->where('name', $name)
                ->where('date', $date)
                ->where('birthDate', $birthDate)
                ->where('birthTime', $birthTime)
                ->where('timezone', $timezone)
                ->where('latitude', $latitude)
                ->where('longitude', $longitude)
                ->where('language', $language)
                ->where('planet', $planet)
                ->where('mahadasha', $mahadasha)
                ->where('antardasha', $antardasha)
                ->where('paryantardasha', $paryantardasha)
                ->where('shookshamadasha', $shookshamadasha)
                ->orderByDesc('id')
                ->first();
                
            if(!$kundali_pdf_data){
                // call API and save the response

                $responses = Http::pool(fn(Pool $pool) => [
                    $pool->as('panchang')->get('https://api.vedicastroapi.com/v3-json/panchang/panchang', [
                        'date' => $date,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
    
                    $pool->as('planetDetails')->get('https://api.vedicastroapi.com/v3-json/horoscope/planet-details', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
    
                    $pool->as('ascendantReport')->get('https://api.vedicastroapi.com/v3-json/horoscope/ascendant-report', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('planetReport')->get('https://api.vedicastroapi.com/v3-json/horoscope/planet-report', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'planet' => $planet,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('findMoonSign')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-moon-sign', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('findSunSign')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-sun-sign', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('findAcendant')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-ascendant', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('extendedKundliDetails')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/extended-kundli-details', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('gemSuggestion')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/gem-suggestion', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('numeroTable')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/numero-table', [
                        'name' => $name,
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('rudrakshSuggestion')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/rudraksh-suggestion', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('shadBala')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/shad-bala', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('friendshipTable')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/friendship', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('kpHouses')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-houses', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('kpPlanets')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-planets', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('kpPlanets')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-planets', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('mangalDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/mangal-dosh', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('KaalsarpDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/kaalsarp-dosh', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('managlikDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('pitraDosh')->get('https://api.vedicastroapi.com/v3-json/dosha/pitra-dosh', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('papasamaya')->get('https://api.vedicastroapi.com/v3-json/dosha/papasamaya', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('currentSadeSati')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/current-sade-sati', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('sadeSatiTable')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/sade-sati-table', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('varshapalDetails')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-details', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('varshapalMonthChart')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-month-chart', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('varshapalYearChart')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-year-chart', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('yogaList')->get('https://api.vedicastroapi.com/v3-json/extended-horoscope/yoga-list', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('charDashaCurrent')->get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-current', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('charDashaMain')->get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-main', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('charDashaSub')->get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-sub', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('yoginiDashaMain')->get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-main', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('yoginiDashaSub')->get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-sub', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('specificDasha')->get('https://api.vedicastroapi.com/v3-json/dashas/specific-sub-dasha', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'md' => $mahadasha,
                        'ad' => $antardasha,
                        'pd' => $paryantardasha,
                        'sd' => $shookshamadasha,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('currentMahadashaFull')->get('https://api.vedicastroapi.com/v3-json/dashas/current-mahadasha-full', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                    
                    $pool->as('currentMahadasha')->get('https://api.vedicastroapi.com/v3-json/dashas/current-mahadasha', [
                        'dob' => $birthDate,
                        'tob' => $birthTime,
                        'tz' => $timezone,
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'api_key' => $api_key,
                        'lang' => $language,
                    ]),
                ]);
    
                $panchang = $responses['panchang']->json() ?? null;
                $planetDetails = $responses['planetDetails']->json() ?? null;
                $ascendantReport = $responses['ascendantReport']->json() ?? null;
                $planetReport = $responses['planetReport']->json() ?? null;
                $findMoonSign = $responses['findMoonSign']->json() ?? null;
                $findSunSign = $responses['findSunSign']->json() ?? null;
                $findAcendant = $responses['findAcendant']->json() ?? null;
                $extendedKundliDetails = $responses['extendedKundliDetails']->json() ?? null;
                $gemSuggestion = $responses['gemSuggestion']->json() ?? null;
                $numeroTable = $responses['numeroTable']->json() ?? null;
                $rudrakshSuggestion = $responses['rudrakshSuggestion']->json() ?? null;
                $shadBala = $responses['shadBala']->json() ?? null;
                $friendshipTable = $responses['friendshipTable']->json() ?? null;
                $kpHouses = $responses['kpHouses']->json() ?? null;
                $kpPlanets = $responses['kpPlanets']->json() ?? null;
                $kpPlanets = $responses['kpPlanets']->json() ?? null;
                $mangalDosh = $responses['mangalDosh']->json() ?? null;
                $KaalsarpDosh = $responses['KaalsarpDosh']->json() ?? null;
                $managlikDosh = $responses['managlikDosh']->json() ?? null;
                $pitraDosh = $responses['pitraDosh']->json() ?? null;
                $papasamaya = $responses['papasamaya']->json() ?? null;
                $currentSadeSati = $responses['currentSadeSati']->json() ?? null;
                $sadeSatiTable = $responses['sadeSatiTable']->json() ?? null;
                $varshapalDetails = $responses['varshapalDetails']->json() ?? null;
                $varshapalMonthChart = $responses['varshapalMonthChart']->json() ?? null;
                $varshapalYearChart = $responses['varshapalYearChart']->json() ?? null;
                $yogaList = $responses['yogaList']->json() ?? null;
                $charDashaCurrent = $responses['charDashaCurrent']->json() ?? null;
                $charDashaMain = $responses['charDashaMain']->json() ?? null;
                $charDashaSub = $responses['charDashaSub']->json() ?? null;
                $yoginiDashaMain = $responses['yoginiDashaMain']->json() ?? null;
                $yoginiDashaSub = $responses['yoginiDashaSub']->json() ?? null;
                $specificDasha = $responses['specificDasha']->json() ?? null;
                $currentMahadashaFull = $responses['currentMahadashaFull']->json() ?? null;
                $currentMahadasha = $responses['currentMahadasha']->json() ?? null;

                KundaliPdfData::create([
                    'kundali_id' => $kundaliId,
                    'name' => $name,
                    'date' => $date,
                    'birthDate' => $birthDate,
                    'birthTime' => $birthTime,
                    'timezone' => $timezone,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'language' => $language,
                    'planet' => $planet,
                    'mahadasha' => $mahadasha,
                    'antardasha' => $antardasha,
                    'paryantardasha' => $paryantardasha,
                    'shookshamadasha' => $shookshamadasha,
                    'panchang_data' => json_encode($panchang),
                    'planet_details_data' => json_encode($planetDetails),
                    'ascendant_report_data' => json_encode($ascendantReport),
                    'planet_report_data' => json_encode($planetReport),
                    'find_moon_sign_data' => json_encode($findMoonSign),
                    'find_sun_sign_data' => json_encode($findSunSign),
                    'find_ascendant_data' => json_encode($findAcendant),
                    'extended_kundli_details_data' => json_encode($extendedKundliDetails),
                    'gem_suggestion_data' => json_encode($gemSuggestion),
                    'numero_table_data' => json_encode($numeroTable),
                    'rudraksh_suggestion_data' => json_encode($rudrakshSuggestion),
                    'shad_bala_data' => json_encode($shadBala),
                    'friendship_table_data' => json_encode($friendshipTable),
                    'kp_houses_data' => json_encode($kpHouses),
                    'kp_planets_data' => json_encode($kpPlanets),
                    'mangal_dosh_data' => json_encode($mangalDosh),
                    'kaalsarp_dosh_data' => json_encode($KaalsarpDosh),
                    'mangalik_dosh_data' => json_encode($managlikDosh),
                    'pitra_dosh_data' => json_encode($pitraDosh),
                    'papasamaya_data' => json_encode($papasamaya),
                    'current_sade_sati_data' => json_encode($currentSadeSati),
                    'sade_sati_table_data' => json_encode($sadeSatiTable),
                    'varshapal_details_data' => json_encode($varshapalDetails),
                    'varshapal_month_chart_data' => json_encode($varshapalMonthChart),
                    'varshapal_year_chart_data' => json_encode($varshapalYearChart),
                    'yoga_list_data' => json_encode($yogaList),
                    'char_dasha_current_data' => json_encode($charDashaCurrent),
                    'char_dasha_main_data' => json_encode($charDashaMain),
                    'char_dasha_sub_data' => json_encode($charDashaSub),
                    'yogini_dasha_main_data' => json_encode($yoginiDashaMain),
                    'yogini_dasha_sub_data' => json_encode($yoginiDashaSub),
                    'specific_dasha_data' => json_encode($specificDasha),
                    'current_mahadasha_full_data' => json_encode($currentMahadashaFull),
                    'current_mahadasha_data' => json_encode($currentMahadasha),
                ]);

            }
            else{
                // get saved response

                $panchang = json_decode($kundali_pdf_data->panchang_data, true) ?? null;
                $planetDetails = json_decode($kundali_pdf_data->planet_details_data, true) ?? null;
                $ascendantReport = json_decode($kundali_pdf_data->ascendant_report_data, true) ?? null;
                $planetReport = json_decode($kundali_pdf_data->planet_report_data, true) ?? null;
                $findMoonSign = json_decode($kundali_pdf_data->find_moon_sign_data, true) ?? null;
                $findSunSign = json_decode($kundali_pdf_data->find_sun_sign_data, true) ?? null;
                $findAcendant = json_decode($kundali_pdf_data->find_ascendant_data, true) ?? null;
                $extendedKundliDetails = json_decode($kundali_pdf_data->extended_kundli_details_data, true) ?? null;
                $gemSuggestion = json_decode($kundali_pdf_data->gem_suggestion_data, true) ?? null;
                $numeroTable = json_decode($kundali_pdf_data->numero_table_data, true) ?? null;
                $rudrakshSuggestion = json_decode($kundali_pdf_data->rudraksh_suggestion_data, true) ?? null;
                $shadBala = json_decode($kundali_pdf_data->shad_bala_data, true) ?? null;
                $friendshipTable = json_decode($kundali_pdf_data->friendship_table_data, true) ?? null;
                $kpHouses = json_decode($kundali_pdf_data->kp_houses_data, true) ?? null;
                $kpPlanets = json_decode($kundali_pdf_data->kp_planets_data, true) ?? null;
                $mangalDosh = json_decode($kundali_pdf_data->mangal_dosh_data, true) ?? null;
                $KaalsarpDosh = json_decode($kundali_pdf_data->kaalsarp_dosh_data, true) ?? null;
                $managlikDosh = json_decode($kundali_pdf_data->mangalik_dosh_data, true) ?? null;
                $pitraDosh = json_decode($kundali_pdf_data->pitra_dosh_data, true) ?? null;
                $papasamaya = json_decode($kundali_pdf_data->papasamaya_data, true) ?? null;
                $currentSadeSati = json_decode($kundali_pdf_data->current_sade_sati_data, true) ?? null;
                $sadeSatiTable = json_decode($kundali_pdf_data->sade_sati_table_data, true) ?? null;
                $varshapalDetails = json_decode($kundali_pdf_data->varshapal_details_data, true) ?? null;
                $varshapalMonthChart = json_decode($kundali_pdf_data->varshapal_month_chart_data, true) ?? null;
                $varshapalYearChart = json_decode($kundali_pdf_data->varshapal_year_chart_data, true) ?? null;
                $yogaList = json_decode($kundali_pdf_data->yoga_list_data, true) ?? null;
                $charDashaCurrent = json_decode($kundali_pdf_data->char_dasha_current_data, true) ?? null;
                $charDashaMain = json_decode($kundali_pdf_data->char_dasha_main_data, true) ?? null;
                $charDashaSub = json_decode($kundali_pdf_data->char_dasha_sub_data, true) ?? null;
                $yoginiDashaMain = json_decode($kundali_pdf_data->yogini_dasha_main_data, true) ?? null;
                $yoginiDashaSub = json_decode($kundali_pdf_data->yogini_dasha_sub_data, true) ?? null;
                $specificDasha = json_decode($kundali_pdf_data->specific_dasha_data, true) ?? null;
                $currentMahadashaFull = json_decode($kundali_pdf_data->current_mahadasha_full_data, true) ?? null;
                $currentMahadasha = json_decode($kundali_pdf_data->current_mahadasha_data, true) ?? null;
            }

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => [
                    'panchang' => $panchang,
                    'planetDetails' => $planetDetails,
                    'ascendantReport' => $ascendantReport,
                    'planetReport' => $planetReport,
                    'findMoonSign' => $findMoonSign,
                    'findSunSign' => $findSunSign,
                    'findAcendant' => $findAcendant,
                    'extendedKundliDetails' => $extendedKundliDetails,
                    'gemSuggestion' => $gemSuggestion,
                    'numeroTable' => $numeroTable,
                    'rudrakshSuggestion' => $rudrakshSuggestion,
                    'shadBala' => $shadBala,
                    'friendshipTable' => $friendshipTable,
                    'kpHouses' => $kpHouses,
                    'kpPlanets' => $kpPlanets,
                    'kpPlanets' => $kpPlanets,
                    'mangalDosh' => $mangalDosh,
                    'KaalsarpDosh' => $KaalsarpDosh,
                    'managlikDosh' => $managlikDosh,
                    'pitraDosh' => $pitraDosh,
                    'papasamaya' => $papasamaya,
                    'currentSadeSati' => $currentSadeSati,
                    'sadeSatiTable' => $sadeSatiTable,
                    'varshapalDetails' => $varshapalDetails,
                    'varshapalMonthChart' => $varshapalMonthChart,
                    'varshapalYearChart' => $varshapalYearChart,
                    'yogaList' => $yogaList,
                    'charDashaCurrent' => $charDashaCurrent,
                    'charDashaMain' => $charDashaMain,
                    'charDashaSub' => $charDashaSub,
                    'yoginiDashaMain' => $yoginiDashaMain,
                    'yoginiDashaSub' => $yoginiDashaSub,
                    'specificDasha' => $specificDasha,
                    'currentMahadashaFull' => $currentMahadashaFull,
                    'currentMahadasha' => $currentMahadasha,
                ]
            ], 200);

        /*    
            $panchang = Http::get('https://api.vedicastroapi.com/v3-json/panchang/panchang', [
                'date' => $date,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
            ->json();

            $planetDetails = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/planet-details', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
            ->json();

            $ascendantReport = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/ascendant-report', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $planetReport = Http::get('https://api.vedicastroapi.com/v3-json/horoscope/planet-report', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'planet' => $planet,
                'lang' => $language,
            ])
                ->json();

            $findMoonSign = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-moon-sign', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $findSunSign = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-sun-sign', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $findAcendant = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/find-ascendant', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $extendedKundliDetails = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/extended-kundli-details', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $gemSuggestion = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/gem-suggestion', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $numeroTable = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/numero-table', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $rudrakshSuggestion = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/rudraksh-suggestion', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $shadBala = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/shad-bala', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $friendshipTable = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/friendship', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $kpHouses = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-houses', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $kpPlanets = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-planets', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $kpPlanets = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/kp-planets', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $mangalDosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/mangal-dosh', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $KaalsarpDosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/kaalsarp-dosh', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $managlikDosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/manglik-dosh', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $pitraDosh = Http::get('https://api.vedicastroapi.com/v3-json/dosha/pitra-dosh', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $papasamaya = Http::get('https://api.vedicastroapi.com/v3-json/dosha/papasamaya', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $currentSadeSati = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/current-sade-sati', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $sadeSatiTable = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/sade-sati-table', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $varshapalDetails = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-details', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $varshapalMonthChart = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-month-chart', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $varshapalYearChart = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/varshapal-year-chart', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $yogaList = Http::get('https://api.vedicastroapi.com/v3-json/extended-horoscope/yoga-list', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $charDashaCurrent = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-current', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $charDashaMain = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-main', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $charDashaSub = Http::get('https://api.vedicastroapi.com/v3-json/dashas/char-dasha-sub', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $yoginiDashaMain = Http::get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-main', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $yoginiDashaSub = Http::get('https://api.vedicastroapi.com/v3-json/dashas/yogini-dasha-sub', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $specificDasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/specific-sub-dasha', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'md' => $mahadasha,
                'ad' => $antardasha,
                'pd' => $paryantardasha,
                'sd' => $shookshamadasha,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $currentMahadashaFull = Http::get('https://api.vedicastroapi.com/v3-json/dashas/current-mahadasha-full', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();

            $currentMahadasha = Http::get('https://api.vedicastroapi.com/v3-json/dashas/current-mahadasha', [
                'dob' => $birthDate,
                'tob' => $birthTime,
                'tz' => $timezone,
                'lat' => $latitude,
                'lon' => $longitude,
                'api_key' => $api_key,
                'lang' => $language,
            ])
                ->json();
        */

        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
