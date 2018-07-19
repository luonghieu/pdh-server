<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TagResource;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use App\Enums\TagType;

class TagController extends ApiController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app(TagRepository::class);
    }

    public function index(Request $request)
    {
        if (! empty($type = $request->type) &&
            ($type == TagType::DESIRE || $type == TagType::SITUATION)
        ) {
            $tags = $this->repository->findByField('type', $type);
        } else {
            $tags = $this->repository->all();
        }

        return $this->respondWithData(TagResource::collection($tags));
    }
}
