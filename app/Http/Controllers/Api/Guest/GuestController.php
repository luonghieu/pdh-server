<?php

namespace App\Http\Controllers\Api\Guest;

use App\Cast;
use App\Enums\CastTransferStatus;
use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Jobs\MakeAvatarThumbnail;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\CastOrderStatus;
use Illuminate\Support\Collection;
use App\Http\Resources\CastResource;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class GuestController extends ApiController
{
    public function castHistories(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        $casts = Cast::join('cast_order as co', function ($query) {
            $query->on('co.user_id', '=', 'users.id')
                ->where('co.status', '=', CastOrderStatus::DONE);
        })->join('orders as o', function ($query) {
            $query->on('o.id', '=', 'co.order_id')
                ->where('o.status', OrderStatus::DONE);
        })->whereHas('orders', function ($query) use ($user) {
            $query->where('orders.user_id', $user->id)
                ->where('orders.status', OrderStatus::DONE);
        });

        if ($request->nickname) {
            $nickname = $request->nickname;
            $casts = $casts->where('nickname', 'like', "%$nickname%");
        }

        $casts = $casts->groupBy('users.id')
            ->orderByDesc('co.updated_at')
            ->orderByDesc('o.updated_at')
            ->select('users.*')
            ->get();

        $casts = $casts->each->setAppends(['latest_order'])
            ->sortByDesc('latest_order.pivot.updated_at')
            ->values();

        $casts = $this->paginate($casts, $request->per_page ?: 15, $request->page);

        $casts = $casts->map(function ($item) {
            $item->latest_order_flag = true;

            return $item;
        });

        return $this->respondWithData(CastResource::collection($casts));
    }

    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ? : (Paginator::resolveCurrentPage() ? : 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function requestTransfer(Request $request)
    {
        $rules = [
            'nickname' => 'max:20|required',
            'date_of_birth' => 'date|before:today|required',
            'job_id' => 'numeric|exists:jobs,id|required',
            'line_qr' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'images' => 'array|required|min:2|max:2',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        try {
            \DB::beginTransaction();
            $user->nickname = $request->nickname;
            $user->date_of_birth = Carbon::parse($request->date_of_birth);
            $user->job_id = $request->job_id;

            $lineImage = $request->file('line_qr');
            if ($lineImage) {
                $lineImageName = Uuid::generate()->string . '.' . strtolower($lineImage->getClientOriginalExtension());
                Storage::put($lineImageName, file_get_contents($lineImage), 'public');
                $user->line_qr = $lineImageName;
            }

            $images = $request->file('images');
            foreach ($images as $image) {
                $imageName = Uuid::generate()->string . '.' . strtolower($image->getClientOriginalExtension());
                Storage::put($imageName, file_get_contents($image), 'public');
                $input = [
                    'is_default' => false,
                    'path' => $imageName,
                    'thumbnail' => ''
                ];
                $avatar = $user->avatars()->create($input);
                MakeAvatarThumbnail::dispatch($avatar);
            }
            $user->type = UserType::CAST;
            $user->cast_transfer_status = CastTransferStatus::PENDING;
            $user->request_transfer_date = Carbon::now();

            $user->save();
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('messages.request_transfer_cast_succeed'));
    }
}
