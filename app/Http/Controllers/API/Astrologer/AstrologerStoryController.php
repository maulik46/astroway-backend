<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerStory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AstrologerStoryController extends Controller
{
    public function addStory(Request $req)
    {
        try {
            // Check if user is authenticated
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            // Validate the request data
            $validator = Validator::make($req->all(), [
                'astrologerId' => 'required',
                'mediaType' => 'required|in:text,image,video',
                'media' => 'required', // Validate media based on media type
            ]);

            // Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }

            // Process media based on media type
            $mediaPaths = [];
            if ($req->mediaType === 'image') {
                foreach ($req->file('media') as $media) {
                    $mediaPath = $this->uploadImage($media);
                    if ($mediaPath) {
                        $mediaPaths[] = $mediaPath;
                    } else {
                        \Log::error('Image upload failed.');
                    }
                }
            } elseif ($req->mediaType === 'video') {
                $mediaPaths[] = $this->uploadVideo($req->file('media'), $req->astrologerId);
            } else {
                $mediaPaths[] = $req->media;
            }

            \Log::info('Media Paths: ' . json_encode($mediaPaths)); // Log media paths

            // Initialize $stories array
            $stories = [];

            // Create a new story for each media path
            foreach ($mediaPaths as $mediaPath) {
                $story = AstrologerStory::create([
                    'astrologerId' => $req->astrologerId,
                    'media' => $mediaPath,
                    'mediaType' => $req->mediaType,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                if ($story) {
                    $stories[] = $story;
                } else {
                    \Log::error('Failed to create story.');
                }
            }

            return response()->json([
                'message' => 'Story added successfully',
                'recordList' => $stories,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    private function uploadImage($image)
    {
        try {
            $name = $image->getClientOriginalName();
            $image->move(public_path('storage/story/images/'), $name);
            return 'public/storage/story/images/' . $name;
        } catch (\Exception $e) {
            // Log or handle the exception
            \Log::error('Error uploading image: ' . $e->getMessage());
            return null; // Return null if there's an error
        }
    }

    private function uploadVideo($video, $astrologerId)
    {
        $time = now()->timestamp;
        $destinationPath = public_path('storage/story/videos/');
        $videoName = 'story_' . $astrologerId . '_' . $time . '.' . $video->getClientOriginalExtension();
        $video->move($destinationPath, $videoName);
        return 'public/storage/story/videos/' . $videoName;
    }




    public function getStory(Request $req)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
        }

        $validator = Validator::make($req->all(), [
            'astrologerId' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {

            $twentyFourHoursAgo = Carbon::now()->subHours(24);
            $stories = AstrologerStory::select('*', DB::raw('(Select Count(story_view_counts.id) as StoryViewCount from story_view_counts where storyId=astrologer_stories.id) as StoryViewCount'))
                ->where('created_at', '>=', $twentyFourHoursAgo)
                ->where('created_at', '<=', Carbon::now())
                ->where('astrologerId', $req->astrologerId)
                ->orderBy('created_at', 'DESC')
                ->get();

            // Decode image paths

            foreach ($stories as $story) {

                // Check if the story has been viewed by the current user
                $story->storyView = DB::table('story_view_counts')->where('storyId', $story->id)
                    ->where('userId', $user->id)
                    ->exists();
            }

            return response()->json([
                'message' => 'Stories Fetch successfully',
                'recordList' => $stories,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }



    public function getAstrologerStory(Request $req)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
        }


        try {
            $twentyFourHoursAgo = Carbon::now()->subHours(24);
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
                        '(CASE WHEN (select Count(story_view_counts.id) from story_view_counts inner join astrologer_stories as sub_story ON sub_story.id = story_view_counts.storyId where sub_story.astrologerId=astrologer_stories.astrologerId AND story_view_counts.userId="' . $user->id . ' ") = COUNT(astrologer_stories.id) THEN 1 ELSE 0 END) as allStoriesViewed'
                    )
                )
                ->groupBy('astrologer_stories.astrologerId', 'astrologers.name', 'astrologers.profileImage')
                ->orderBy('latest_story_date', 'DESC')
                ->get();


            return response()->json([
                'message' => 'Stories Fetch successfully',
                'recordList' => $stories,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }




    public function clickStory(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only('userId', 'storyId');

            $validator = Validator::make($data, [
                'storyId' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

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
