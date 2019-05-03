<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\TimelineFavoritesResource;
use App\Http\Resources\TimeLineResource;
use App\Services\LogService;
use App\TimeLine;
use App\TimeLineFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class TimeLineController extends ApiController
{
    public function index(Request $request)
    {
        $user = $this->guard()->user();

        $id = $user->id;
        if ($request->user_id) {
            $id = $request->user_id;
        }

        $timeLine = TimeLine::where('user_id', $id)->where('hidden', false)->paginate(10);

        return $this->respondWithData(TimeLineResource::collection($timeLine));
    }

    public function show($id)
    {
        $timeLine = TimeLine::find($id);

        return $this->respondWithData(TimeLineResource::make($timeLine));
    }

    public function favorites($id)
    {
        $timeLine = TimeLine::find($id);
        $timelineFavorites = $timeLine->favorites;

        return $this->respondWithData(TimelineFavoritesResource::collection($timelineFavorites));
    }

    public function create(Request $request)
    {
        $rules = [
            'content' => 'required|string|max:240',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'location' => 'required|string|max:20',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        try {
            $input = [
                'user_id' => $user->id,
                'content' => $request->content,
                'location' => $request->location,
            ];

            if ($request->has('image')) {
                $image = $request->file('image');
                $imageName = Uuid::generate()->string . '.jpg';
                $image = \Image::make($image)->encode('jpg', 75);
                $fileUploaded = Storage::put($imageName, $image->__toString(), 'public');

                if ($fileUploaded) {
                    $input['image'] = $imageName;
                }
            }

            $timeLine = TimeLine::create($input);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithData(TimeLineResource::make($timeLine));
    }

    public function updateFavorite($id)
    {
        $timeline = TimeLine::find($id);

        if (!$timeline) {
            return $this->respondErrorMessage(trans('messages.timeline_not_found'), 404);
        }

        $user = $this->guard()->user();

        try {
            $favorite = $timeline->favorites()->where('user_id', $user->id);

            if ($favorite->exists()) {
                $favorite->delete();

                return $this->respondWithNoData(trans('messages.unfavorite_success'));
            }

            $favorite = new TimeLineFavorite();
            $favorite->time_line_id = $timeline->id;
            $favorite->user_id = $user->id;
            $favorite->save();
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.favorite_success'));
    }
}
