<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AvatarResource;
use App\Jobs\MakeAvatarThumbnail;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class AvatarController extends ApiController
{
    public function upload(Request $request)
    {
        $user = $this->guard()->user();

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $input['is_default'] = true;
        $input['thumbnail'] = '';

        if (request()->has('image')) {
            $image = request()->file('image');
            $imageName = Uuid::generate()->string . '.' . strtolower($image->getClientOriginalExtension());
            $fileUploaded = Storage::put($imageName, file_get_contents($image), 'public');

            if ($fileUploaded) {
                $input['path'] = $imageName;
            }
        }

        try {
            $avatar = $user->avatars()->create($input);

            MakeAvatarThumbnail::dispatch($avatar);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }

        return $this->respondWithData(AvatarResource::make($avatar));
    }
}
