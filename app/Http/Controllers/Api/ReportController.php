<?php

namespace App\Http\Controllers\Api;

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

        try {
            $report = Report::create($input);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e->getMessage());
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.report_success'));
    }
}
