<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\TimelineFavoritesResource;
use App\Http\Resources\TimeLineResource;
use App\Services\LogService;
use App\TimeLine;
use App\TimeLineFavorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;
use App\Notifications\NotifyFavouriteTimeline;

class TimeLineController extends ApiController
{
    public function index(Request $request)
    {
        $user = $this->guard()->user();

        $id = $user->id;

        $timeLine = TimeLine::query();
        if ($request->user_id) {
            if ($id == $request->user_id) {
                $timeLine = $timeLine->where('user_id', $id);
            } else {
                $timeLine = $timeLine->where('user_id', $request->user_id)->where('hidden', false);
            }
        } else {
            $timeLine = $timeLine->where('hidden', false);
        }

        $perPage = 10;
        if ($request->per_page) {
            $perPage = $request->per_page;
        }

        $timeLine = $timeLine->latest()->paginate($perPage);

        return $this->respondWithData(TimeLineResource::collection($timeLine));
    }

    public function show($id)
    {
        $timeLine = TimeLine::find($id);

        return $this->respondWithData(TimeLineResource::make($timeLine));
    }

    public function favorites(Request $request, $id)
    {
        $timeLine = TimeLine::find($id);
        if (!$timeLine) {
            return $this->respondErrorMessage(trans('messages.timeline_not_found'));
        }

        $perPage = 10;
        if ($request->per_page) {
            $perPage = $request->per_page;
        }

        $timelineFavorites = $timeLine->favorites()->paginate($perPage);

        return $this->respondWithData(TimelineFavoritesResource::collection($timelineFavorites));
    }

    public function create(Request $request)
    {
        $rules = [
            'content' => 'string|max:240',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'location' => 'string|max:20',
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

                return $this->respondWithData(TimeLineResource::make($timeline));
            }

            $favorite = new TimeLineFavorite();
            $favorite->time_line_id = $timeline->id;
            $favorite->user_id = $user->id;
            $favorite->save();
            if ($timeline->user_id != $user->id) {
                \Notification::send($user, new NotifyFavouriteTimeline($timeline->user_id));
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithData(TimeLineResource::make($timeline));
    }

    public function delete($id)
    {
        $user = $this->guard()->user();
        $timeline = $user->timelines()->find($id);

        if (!$timeline) {
            return $this->respondErrorMessage(trans('messages.timeline_not_found'));
        }

        $timeline->delete();

        return $this->respondWithNoData(trans('messages.timeline_deleted'));
    }
}
