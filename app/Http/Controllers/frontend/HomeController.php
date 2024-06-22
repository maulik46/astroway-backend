<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\AstrologerModel\AstrologerStory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        Artisan::call('cache:clear');
        $session = new Session();
        $token = $session->get('token');
        // dd(Auth::guard('api')->user());
        // dd($token);

        $home_data = Http::withoutVerifying()->post(env('APP_URL') . '/api/getCustomerHome')->json();
        // dd($home_data);
        $liveastro = Http::withoutVerifying()->post(env('APP_URL') . '/api/liveAstrologer/get')->json();
        $getAstrologer = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstrologer')->json();
        $getAstromallProduct = Http::withoutVerifying()->post(env('APP_URL') . '/api/getAstromallProduct')->json();

        $banner_data = collect($home_data['banner']);

        // Shuffle the collection and get the first item
        $random_banner = $banner_data->shuffle()->first();

        $getsystemflag = Http::withoutVerifying()->post(env('APP_URL') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);

        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
        $freekundali = $getsystemflag->where('name', 'FreeKundali')->first();
        $kundali_matching = $getsystemflag->where('name', 'KundaliMatching')->first();
        $panchang = $getsystemflag->where('name', 'TodayPanchang')->first();

        $blog = $getsystemflag->where('name', 'Blog')->first();
        $shop = $getsystemflag->where('name', 'Astromall')->first();
        $daily_horoscope = $getsystemflag->where('name', 'DailyHoroscope')->first();


        // Stories

        $twentyFourHoursAgo = Carbon::now()->subHours(24);

        if (authcheck()) {
            $id = authcheck()['id'];
            $stories = AstrologerStory::join('astrologers', 'astrologers.id', '=', 'astrologer_stories.astrologerId')
                ->where('astrologer_stories.created_at', '>=', $twentyFourHoursAgo)
                ->where('astrologer_stories.created_at', '<=', Carbon::now())
                ->select(
                    'astrologer_stories.astrologerId',
                    'astrologers.name',
                    'astrologers.profileImage',
                    DB::raw('COUNT(astrologer_stories.id) as story_count'),
                    DB::raw('MAX(astrologer_stories.created_at) as latest_story_date'),

                    DB::raw(
                        '(CASE WHEN (select Count(story_view_counts.id) from story_view_counts inner join astrologer_stories as sub_story ON sub_story.id = story_view_counts.storyId where sub_story.astrologerId=astrologer_stories.astrologerId AND story_view_counts.userId="' . $id . ' ") = COUNT(astrologer_stories.id) THEN 1 ELSE 0 END) as allStoriesViewed'
                    )
                )
                ->groupBy('astrologer_stories.astrologerId', 'astrologers.name', 'astrologers.profileImage')
                ->orderBy('latest_story_date', 'DESC')
                ->get();
        } else {
            $stories = AstrologerStory::join('astrologers', 'astrologers.id', '=', 'astrologer_stories.astrologerId')
                ->where('astrologer_stories.created_at', '>=', $twentyFourHoursAgo)
                ->where('astrologer_stories.created_at', '<=', Carbon::now())
                ->select(
                    'astrologer_stories.astrologerId',
                    'astrologers.name',
                    'astrologers.profileImage',
                    DB::raw('COUNT(astrologer_stories.id) as story_count'),
                    DB::raw('MAX(astrologer_stories.created_at) as latest_story_date')
                )
                ->groupBy('astrologer_stories.astrologerId', 'astrologers.name', 'astrologers.profileImage')
                ->orderBy('latest_story_date', 'DESC')
                ->get();
        }




        $getstoriesbyid = AstrologerStory::select('*', DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('astrologerId', $request->astrologerId)
            ->orderBy('created_at', 'DESC')
            ->get();


        return view('frontend.pages.index', [
            'token' => $token,
            'home_data' => $home_data,
            'liveastro' => $liveastro,
            'getAstrologer' => $getAstrologer,
            'getAstromallProduct' => $getAstromallProduct,
            'random_banner' => $random_banner,
            'currency' => $currency,
            'freekundali' => $freekundali,
            'kundali_matching' => $kundali_matching,
            'blog' => $blog,
            'shop' => $shop,
            'panchang' => $panchang,
            'daily_horoscope' => $daily_horoscope,
            'stories' => $stories,
            'getstoriesbyid' => $getstoriesbyid,

        ]);
    }

    public function getAstrologerStories($id)
    {
        $twentyFourHoursAgo = Carbon::now()->subHours(24);


        $stories = AstrologerStory::select('*', DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'))
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('astrologerId', $id)
            ->orderBy('created_at', 'DESC')
            ->get();


        return response()->json($stories);
    }



    public function viewstory(Request $req)
    {
        try {

            $id = authcheck()['id'] ? authcheck()['id'] : 0;
            // Check if the combination of userId and storyId already exists
            if (DB::table('story_view_counts')->where('userId', $id)->where('storyId', $req->storyId)->exists()) {
                return response()->json(['message' => 'Story already viewed', 'status' => 200], 200);
            }

            // Insert data into story_view_counts table
            $countview = DB::table('story_view_counts')->insert([
                'userId' => $id,
                'storyId' => $req->storyId,
            ]);

            if ($countview) {
                return response()->json(['message' => 'Story Viewed successfully', 'status' => 200], 200);
            } else {
                return response()->json(['error' => 'Failed to insert data', 'status' => 500], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
