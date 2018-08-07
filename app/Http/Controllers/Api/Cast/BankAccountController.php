<?php

namespace App\Http\Controllers\Api\Cast;

use App\Cast;
use App\Http\Controllers\Api\ApiController;
use App\Services\LogService;
use Illuminate\Http\Request;

class BankAccountController extends ApiController
{
    public function update(Request $request, $id)
    {
        $cast = Cast::find($this->guard()->user()->id);
        $bankAccount = $cast->bankAccount()->find($id);

        if (!$bankAccount) {
            return $this->respondErrorMessage(trans('messages.account_not_exists'), 404);
        }

        $rules = [
            'bank_name' => 'required',
            'number' => 'required',
            'holder_name' => 'required',
            'type' => 'required|in:1,2',
            'bank_code' => 'required',
            'branch_name' => 'required',
            'branch_code' => 'required',
        ];

        $validator = validator($request->all(), $rules);
        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $input = $request->only([
            'bank_name',
            'number',
            'holder_name',
            'type',
            'bank_code',
            'branch_name',
            'branch_code',
        ]);

        try {
            $cast->bankAccount()->update($input);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.update_success'));
    }
}
