<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use Illuminate\Http\Request;
use App\Http\Resources\CastResource;
use App\Http\Resources\GuestResource;
use App\Repositories\UserRepository;

class UserController extends ApiController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app(UserRepository::class);
    }

    public function show(Request $request)
    {
        try {
            $userId = $request->id;
            $user = $this->repository->find($userId);
            if (UserType::CAST == $user->type) {
                return $this->respondWithData(new CastResource($user));
            }
            return $this->respondWithData(new GuestResource($user));
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                return $this->respondErrorMessage(trans('messages.user_not_found'), $e->getCode());
            }
            return $this->respondServerError();
        }
    }
}
