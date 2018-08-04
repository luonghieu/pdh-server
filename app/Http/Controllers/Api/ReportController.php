<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoomType;
use App\Room;
use App\Services\LogService;
use App\User;
use App\Report;
use Illuminate\Http\Request;

class ReportController extends ApiController
{
    public function report(Request $request, User $user)
    {
        $rules = [
            'reported_id' => 'required|numeric|exists:users,id',
            'content' => 'required',
        ];

        $data = array_merge($request->all(), [
            'reported_id' => $request->route('id'),
        ]);

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $input = $request->only([
            'content',
        ]);
        $input['reported_id'] = $request->route('id');
        $input['user_id'] = $this->guard()->id();

        $userId = $this->guard()->user()->id;
        $reportedId = $input['reported_id'];

        $room = Room::where('type', RoomType::DIRECT)->where(function($q) use ($userId, $reportedId) {
           $q->where('owner_id', $userId)->whereHas('users', function($subQuery) use ($reportedId) {
               $subQuery->where('user_id', $reportedId);
           });
        })->orWhere(function($q) use ($userId, $reportedId) {
            $q->where('owner_id', $reportedId)->whereHas('users', function($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId);
            });
        })->first();

        $input['room_id'] = $room->id;
        try {
            $report = Report::create($input);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e->getMessage());
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.report_success'));
    }
}
