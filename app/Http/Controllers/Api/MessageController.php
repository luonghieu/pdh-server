<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\MessageResource;
use App\Message;
use App\Room;
use App\Services\LogService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class MessageController extends ApiController
{
    public function delete($id)
    {
        $user = $this->guard()->user();

        $message = $user->messages()->find($id);

        if (!$message) {
            return $this->respondErrorMessage(trans('messages.message_exits'), 409);
        }

        $message->delete($id);

        return $this->respondWithNoData(trans('messages.delete_message_success'));
    }

    public function store(Request $request, $id)
    {
        $rules = [
            'message' => 'required_if:type,2',
            'image' => 'required_if:type,3|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'type' => 'required',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }
        $room = Room::active()->find($id);

        if (!$room) {
            return $this->respondErrorMessage(trans('messages.room_not_found'), 404);
        }

        $message = new Message;
        $message->room_id = $id;
        $message->user_id = $this->guard()->id();
        $message->type = $request->type;

        if ($request->message) {
            $message->message = $request->message;
        }

        if (request()->has('image')) {
            $image = request()->file('image');
            $imageName = Uuid::generate()->string . '.' . strtolower($image->getClientOriginalExtension());
            $fileUploaded = Storage::put($imageName, file_get_contents($image), 'public');

            if ($fileUploaded) {
                $message->image = $imageName;
            }
        }

        try {
            $user = $this->guard()->user();

            $userIds = User::where('id', '!=', $user->id)->whereHas('rooms', function ($query) use ($id) {
                $query->where('room_id', $id);
            })->pluck('id')->toArray();

            $message->save();

            $message->recipients()->attach($userIds, ['room_id' => $id]);

            $message = $message->load('user');
        } catch (\Exception $e) {

            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithData(MessageResource::make($message));
    }
}
