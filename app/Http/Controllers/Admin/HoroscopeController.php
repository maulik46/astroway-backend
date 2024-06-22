<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\HororscopeSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Horoscope;
use Carbon\Carbon;
use DateTime;

define('LOGINPATH', '/admin/login');
define('DATEFORMAT', "(DATE_FORMAT(date,'%Y-%m-%d'))");

class HoroscopeController extends Controller
{
    protected $aDate;
	public $path;
    public $limit = 15;
    public $paginationStart;



    public function __construct(){
        $this->aDate = $this->getThisWeekDate();
    }

    public function generateDailyHorscope()
    {

        $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();

        $currDate = date('d/m/Y');
        for ($i=1; $i <= 12 ; $i++) {

            foreach (['en','ta','ka','te','hi','ml','sp','fr'] as $langkey => $langvalue)
            {
                $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/prediction/daily-moon', [
                    'zodiac' => $i,
                    'date' => $currDate,
                    'show_same' => true,
                    'api_key' => $api_key->value,
                    'lang' => $langvalue,
                ]);


                $data = $dailyHorscope->json();



                Horoscope::create([
                    'zodiac' => $data['response']['zodiac'],
                    'total_score' => $data['response']['total_score'],
                    'lucky_color' => $data['response']['lucky_color'],
                    'lucky_color_code' => $data['response']['lucky_color_code'],
                    'lucky_number' => json_encode($data['response']['lucky_number']),
                    'physique' => $data['response']['physique'],
                    'status' => $data['response']['status'],
                    'finances' => $data['response']['finances'],
                    'relationship' => $data['response']['relationship'],
                    'career' => $data['response']['career'],
                    'travel' => $data['response']['travel'],
                    'family' => $data['response']['family'],
                    'friends' => $data['response']['friends'],
                    'health' => $data['response']['health'],
                    'bot_response' => $data['response']['bot_response'],
                    'date' => date('Y-m-d'),
                    'end_date' => null,
                    'start_date' => null,
                    'type' => config('constants.DAILY_HORSCOPE'),
                    'langcode' => $langvalue,
                ]);
            }


        }
        return response()->json(['message' => 'Horoscope stored successfully']);
    }

    public function generateWeeklyHorscope()
    {
        $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();
        $currDate = date('Y-m-d');
        for ($i=1; $i <= 12 ; $i++) {

            foreach (['en','ta','ka','te','hi','ml','sp','fr'] as $langkey => $langvalue)
            {
                $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/prediction/weekly-moon', [
                    'zodiac' => $i,
                    'week' => "thisweek",
                    'show_same' => true,
                    'api_key' => $api_key->value,
                    'lang' => $langvalue,
                ]);

                $data = $dailyHorscope->json();
                // dd($data);
                Horoscope::create([
                    'zodiac' => $data['response']['zodiac'],
                    'total_score' => $data['response']['total_score'],
                    'lucky_color' => $data['response']['lucky_color'],
                    'lucky_color_code' => $data['response']['lucky_color_code'],
                    'lucky_number' => json_encode($data['response']['lucky_number']),
                    'physique' => isset($data['response']['physique']) ? $data['response']['physique'] : 0,
                    'status' => $data['response']['status'],
                    'finances' => $data['response']['finances'],
                    'relationship' => $data['response']['relationship'],
                    'career' => $data['response']['career'],
                    'travel' => $data['response']['travel'],
                    'family' => $data['response']['family'],
                    'friends' => $data['response']['friends'],
                    'health' => $data['response']['health'],
                    'bot_response' => $data['response']['bot_response'],
                    'date' => $currDate,
                    'type' => config('constants.WEEKLY_HORSCOPE'),
                    'start_date' => $this->aDate['startdate'],
                    'end_date' => $this->aDate['enddate'],
                    'langcode' => $langvalue,
                ]);
            }
        }
        return response()->json(['message' => 'Horoscope stored successfully']);
    }

    public function generateYearlyHorscope()
    {
        $api_key=DB::table('systemflag')->where('name','vedicAstroAPI')->first();
        $currDate = date('Y-m-d');
        for ($i=1; $i <= 12 ; $i++) {
            foreach (['en','ta','ka','te','hi','ml','fr','sp'] as $langkey => $langvalue)
            {
                $dailyHorscope = Http::get('https://api.vedicastroapi.com/v3-json/prediction/yearly', [
                    'zodiac' => $i,
                    'year' => date('Y'),
                    'show_same' => true,
                    'api_key' => $api_key->value,
                    'lang' => $langvalue,
                ]);

                $data = $dailyHorscope->json();


                if (!isset($data['response'])) {
                    continue;
                }

                foreach ($data['response'] as $key => $value) {
                    $zodiac = "";
                    if($i == 1){
                        $zodiac = "Aries";
                    }else if($i == 2){
                        $zodiac = "Taurus";
                    }else if($i == 3){
                        $zodiac = "Gemini";
                    }else if($i == 4){
                        $zodiac = "Cancer";
                    }else if($i == 5){
                        $zodiac = "Leo";
                    }else if($i == 6){
                        $zodiac = "Virgo";
                    }else if($i == 7){
                        $zodiac = "Libra";
                    }else if($i == 8){
                        $zodiac = "Scorpio";
                    }else if($i == 9){
                        $zodiac = "Sagittarius";
                    }else if($i == 10){
                        $zodiac = "Capricorn";
                    }else if($i == 11){
                        $zodiac = "Aquarius";
                    }else if($i == 12){
                        $zodiac = "Pisces";
                    }



                    if($langvalue=='en')
                        list($startDate, $endDate) = explode(" to ", $value['period']);

                    $startDate = date('Y-m-d', strtotime($startDate));
                    $endDate = date('Y-m-d', strtotime($endDate));
                    Horoscope::create([
                        'zodiac' => $zodiac,
                        'total_score' => isset($value['score']) ? substr($value['score'], 0 ,-1) : 0,
                        'lucky_color' => isset($value['lucky_color']) ? $value['lucky_color'] : 0,
                        'lucky_color_code' => isset($value['lucky_color_code']) ? $value['lucky_color_code'] : '',
                        'lucky_number' => isset($value['lucky_number']) ? $value['lucky_number'] : 0,
                        'physique' => isset($value['physique']) ? $value['physique'] : 0,
                        'status' => isset($value['status']['score']) ? substr($value['status']['score'],0,-1) : 0,
                        'finances' => isset($value['finances']['score']) ? substr($value['finances']['score'],0,-1) : 0,
                        'relationship' => isset($value['relationship']['score']) ? substr($value['relationship']['score'],0,-1) : 0,
                        'career' => isset($value['career']['score']) ? substr($value['career']['score'],0,-1) : 0,
                        'travel' => isset($value['travel']['score']) ? substr($value['travel']['score'],0,-1) : 0,
                        'family' => isset($value['family']['score']) ? substr($value['family']['score'],0,-1) : 0,
                        'friends' => isset($value['friends']['score']) ? substr($value['friends']['score'],0,-1) : 0,
                        'health' => isset($value['health']['score']) ? substr($value['health']['score'],0,-1) : 0,
                        'bot_response' => str_replace("'", "",$value['prediction']),
                        'date' => $currDate,
                        'type' => config('constants.YEARLY_HORSCOPE'),
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'health_remark' => $value['career']['prediction'],
                        'relationship_remark' => isset($value['relationship']['prediction']) ? $value['relationship']['prediction'] : "",
                        'travel_remark' => isset($value['travel']['prediction']) ? $value['travel']['prediction'] : "",
                        'family_remark' => isset($value['family']['prediction']) ? $value['family']['prediction'] : "",
                        'friends_remark' => isset($value['friends']['prediction']) ? $value['friends']['prediction'] : "",
                        'finances_remark' => isset($value['finances']['prediction']) ? $value['finances']['prediction'] : "",
                        'status_remark' => isset($value['status']['prediction']) ? $value['status']['prediction'] : "",
                        'langcode' => $langvalue,
                    ]);
                }
            }

        }
        return response()->json(['message' => 'Horoscope stored successfully']);
    }

    public function getThisWeekDate()
    {
        date_default_timezone_set('asia/kolkata');
        // Get the current date
        $currentDate = new DateTime();
        // Get the start date of the week (Sunday)
        $startDate = clone $currentDate;
        $startDate->modify('this week');
        // Get the end date of the week (Saturday)
        $endDate = clone $startDate;
        $endDate->modify('this week +6 days');
        // Format the dates as needed
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
        $aDate['startdate'] = $startDateFormatted;
        $aDate['enddate'] = $endDateFormatted;
        return $aDate;
    }

   public function getHoroscope(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {

                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $dt = Carbon::now()->format('Y-m-d');

                $filterDate = $request->filterDate ? Carbon::parse($request->filterDate)->format('Y-m-d') : Carbon::Now()->format('Y-m-d');

                $currentDate = new DateTime();
                $currentDate->setISODate((int)$currentDate->format('o'), (int)$currentDate->format('W'), 1); // Set to the first day of the current week
                $startOfWeekFormatted = $currentDate->format('Y-m-d');

                $currentDate->modify('+6 days'); // Move to the last day of the current week
                $endOfWeekFormatted = $currentDate->format('Y-m-d');

                $Horoscope = Horoscope::selectRaw('horoscopes.*, REPLACE(lucky_color_code, "#", "0xff") AS color_code')
                    ->whereBetween('start_date', [$startOfWeekFormatted, $endOfWeekFormatted])
                    ->where('type', config('constants.WEEKLY_HORSCOPE'))
                    ->orderBy('created_at', 'DESC');

                $dailyHoroscopecount = $Horoscope->count();
                $totalRecords = $dailyHoroscopecount;
                $totalPages = ceil($dailyHoroscopecount / $this->limit);
                $searchString = $request->searchString ? $request->searchString : null;

                if ($request->filterDate) {
                    $filterDate = Carbon::parse($request->filterDate)->format('Y-m-d');
                    $Horoscope->where(DB::raw(DATEFORMAT), $filterDate);
                }

                $Horoscope = $Horoscope->skip($paginationStart)->take($this->limit);
                $dailyHoroscope = $Horoscope->get();



                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;



                return view('pages.horoscope', compact('dailyHoroscope',  'filterDate','start', 'end', 'page','totalRecords','searchString','totalPages'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    public function addHoroscope(Request $req)
    {
        try {
            // return response()->json([
            //     'error' => ["This Option is disabled for Demo!"],
            // ]);
            // return back()->with('error', 'This Option is disabled for Demo!');
            if (Auth::guard('web')->check()) {
                $validator = Validator::make($req->all(), [
                    'horoscopeSignId' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->getMessageBag()->toArray(),
                    ]);
                }
                $this->addHoro('Weekly', $req->title, $req->horoscopeSignId, $req->weeklydesc, null);
                $this->addHoro('Monthly', $req->monthlytitle, $req->horoscopeSignId, $req->monthlydesc, null);
                $this->addHoro('Yearly', $req->yearlytitle, $req->horoscopeSignId, $req->yearlydesc, null);
                return response()->json([
                    'success' => "Add Horoscope Successfully",
                ]);
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function addHoro($hroscopeType, $horoTitle, $horoscopeSignId, $description, $oldSignId)
    {
        // return back()->with('error', 'This Option is disabled for Demo!');
        $horoscope = DB::table('horoscope')->where('horoscopeSignId', $horoscopeSignId)->where('horoscopeType', $hroscopeType)->get();
        $data = array(
            'horoscopeType' => $hroscopeType,
            'title' => $horoTitle,
            'description' => $description,
            'horoscopeSignId' => $horoscopeSignId,
        );
        if ($horoscope && count($horoscope) > 0) {
            DB::table('horoscope')->where('id', $horoscope[0]->id)->update($data);
        } else {
            if ($oldSignId) {
                DB::table('horoscope')->where('horoscopeSignId', $oldSignId)->delete();
            }
            DB::table('horoscope')
                ->insert($data);
        }
    }

    public function editHoroscope(Request $req)
    {
        try {
            // return response()->json([
            //     'error' => ["This Option is disabled for Demo!"],
            // ]);
            if (Auth::guard('web')->check()) {
                $validator = Validator::make($req->all(), [
                    'horoscopeSignId' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->getMessageBag()->toArray(),
                    ]);
                }
                $this->addHoro('Weekly', $req->title, $req->horoscopeSignId, $req->weeklydesc, $req->oldSignId);
                $this->addHoro('Monthly', $req->monthlytitle, $req->horoscopeSignId, $req->monthlydesc, $req->oldSignId);
                $this->addHoro('Yearly', $req->yearlytitle, $req->horoscopeSignId, $req->yearlydesc, $req->oldSignId);
                return response()->json([
                    'success' => "Edit Horoscope Successfully",
                ]);
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteHoroscope(Request $request)
    {
        try {
            // return back()->with('error', 'This Option is disabled for Demo!');
            if (Auth::guard('web')->check()) {
                DB::table('horoscope')->where('horoscopeSignId', '=', $request->del_id)->delete();
                return redirect()->route('horoscope');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function redirectAddHoroscope()
    {
        $hororscopeSign = HororscopeSign::query();
        $signs = $hororscopeSign->where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.add-horoscope', compact('signs'));
    }

    public function redirectEditHoroscope(Request $req)
    {
        $horoscopeSignId = $req->horoscopeSignId;
        $horoscope = DB::table('horoscope')->where('horoscopeSignId', $req->horoscopeSignId)->get();
        if ($horoscope && count($horoscope) > 0) {
            for ($i = 0; $i < count($horoscope); $i++) {
                if ($horoscope[$i]->horoscopeType == 'Weekly') {
                    $weeklytitle = $horoscope[$i]->title;
                    $weeklydesc = $horoscope[$i]->description;
                }

                if ($horoscope[$i]->horoscopeType == 'Monthly') {
                    $monthlytitle = $horoscope[$i]->title;
                    $monthlydesc = $horoscope[$i]->description;
                }

                if ($horoscope[$i]->horoscopeType == 'Yearly') {
                    $yearlytitle = $horoscope[$i]->title;
                    $yearlydesc = $horoscope[$i]->description;
                }
            }
        }
        $horo = array(
            'weeklytitle' => $weeklytitle,
            'weeklydesc' => $weeklydesc,
            'monthlytitle' => $monthlytitle,
            'monthlydesc' => $monthlydesc,
            'yearlytitle' => $yearlytitle,
            'yearlydesc' => $yearlydesc,
        );
        $hororscopeSign = HororscopeSign::query();
        $signs = $hororscopeSign->where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.edit-horoscope', compact('signs', 'horo', 'horoscopeSignId'));
    }
	public function getyearlyHoroscope(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {

                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $dt = Carbon::now()->format('Y-m-d');

                $filterDate = $request->filterDate ? Carbon::parse($request->filterDate)->format('Y-m-d') : Carbon::Now()->format('Y-m-d');

                $currentDate = new DateTime();
                $currentYear = $currentDate->format('Y');
               $startOfYear = new DateTime("$currentYear-01-01");
                $startOfYearFormatted = $startOfYear->format('Y-m-d');
                $endOfYear = new DateTime("$currentYear-12-31");
                $endOfYearFormatted = $endOfYear->format('Y-m-d');
                // dd($startOfYearFormatted,$endOfYearFormatted);

                $Horoscope = DB::table('horoscopes')->selectRaw('horoscopes.*, REPLACE(lucky_color_code, "#", "0xff") AS color_code')->where('start_date', '>=', $startOfYearFormatted)->where('end_date', '<=', $endOfYearFormatted)->where('type', config('constants.YEARLY_HORSCOPE'))->orderBy('created_at', 'DESC');

            //   dd($Horoscope->get());

                $dailyHoroscopecount = $Horoscope->count();

                $totalRecords = $dailyHoroscopecount;
                $totalPages = ceil($dailyHoroscopecount / $this->limit);
                $searchString = $request->searchString ? $request->searchString : null;

                if ($request->filterDate) {
                    $filterDate = Carbon::parse($request->filterDate)->format('Y-m-d');
                    $dailyHoroscope = $Horoscope->where(DB::raw(DATEFORMAT), $filterDate);

                } else {
                    $dt = Carbon::now()->format('Y-m-d');
                    // $dailyHoroscope = $Horoscope->where(DB::raw(DATEFORMAT), $dt);

                }

                $Horoscope = $Horoscope->skip($paginationStart);
                $Horoscope = $Horoscope->take($this->limit);
                $dailyHoroscope = $Horoscope->get();



                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;



                return view('pages.yearlyhoroscope', compact('dailyHoroscope',  'filterDate','start', 'end', 'page','totalRecords','searchString','totalPages'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
