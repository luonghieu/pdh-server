<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoomType;
use App\Enums\UserType;
use App\Http\Resources\RoomResource;
use App\Room;
use App\Services\LogService;
use App\User;
use DB;
use Illuminate\Http\Request;
use Storage;

class RoomController extends ApiController
{
    public function index(Request $request)
    {
        $rules = [
            'per_page' => 'numeric|min:1',
            'favorited' => 'boolean',
            'nickname' => 'max:20',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $rooms = Room::active()->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        $nickName = $request->nickname;
        if ($nickName) {
            $rooms->whereHas('users', function ($query) use ($nickName) {
                $query->where('users.nickname', 'like', "%$nickName%");
            });
        }

        if ($request->favorited) {
            $isFavoritedIds = $user->favorites()->pluck('favorited_id')->toArray();

            $rooms->where('type', RoomType::DIRECT)->whereHas('users', function ($query) use ($isFavoritedIds) {
                $query->whereIn('user_id', $isFavoritedIds);
            });
        }

        $rooms = $rooms->with('latestMessage', 'users')->orderBy('updated_at', 'DESC')->paginate($request->per_page)->appends($request->query());

        if ('html' == $request->response_type) {
            $rooms = $this->respondWithData(RoomResource::collection($rooms));
            $rooms = $rooms->getData()->data;

            if (!$rooms->data) {
                return view('web.rooms.no_room');
            }
            return view('web.rooms.content-room', compact('rooms'));
        }

        return $this->respondWithData(RoomResource::collection($rooms));
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();
        if (!$user->status) {
            return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
        }

        $userId = $request->user_id;
        $type = $request->type;

        if (User::find($userId)->type == $user->type) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        if (!$type) {
            $type = RoomType::DIRECT;
        }

        try {
            $room = $user->rooms()->direct()->whereHas('users', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->first();

            if (!$room) {
                $room = new Room;
                $room->type = $type;
                $room->is_active = true;
                $room->owner_id = $user->id;
                $room->save();

                $room->users()->attach([$userId, $user->id]);
            }

            return $this->respondWithData(RoomResource::make($room));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    public function getRooms(Request $request)
    {
        $search = $request->search;

        $rooms = DB::table('rooms')->where('is_active', true)
            ->where('rooms.type', RoomType::SYSTEM);

        if ($request->search || $request->ids) {
            if ($search) {
                $rooms = $rooms->join('users', function ($j) use ($search) {
                    $j->on('rooms.owner_id', '=', 'users.id')
                        ->where(function ($w) use ($search) {
                            $w->Where('users.id', 'like', '%' . $search . '%')
                                ->orWhere('users.nickname', 'like', '%' . $search . '%');
                        });
                });
            }

            if ($request->ids) {
                $ids = explode(',', trim($request->ids, ','));
                $rooms = $rooms->whereIn('rooms.id', $ids)->join('users', 'rooms.owner_id', '=', 'users.id');
            }

            $rooms = $rooms->leftJoin('avatars', function ($j) {
                $j->on('avatars.user_id', '=', 'users.id')
                    ->where('is_default', true);
            })->where('users.deleted_at', null)
                ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
                ->orderBy('users.updated_at', 'DESC')->get();
        } else {
            $roomGuests = DB::table('rooms')->where('is_active', true)
                ->where('rooms.type', RoomType::SYSTEM)
                ->join('users', function ($j) {
                    $j->on('rooms.owner_id', '=', 'users.id')
                        ->where('users.type', UserType::GUEST);
                })
                ->leftJoin('avatars', function ($j) {
                    $j->on('avatars.user_id', '=', 'users.id')
                        ->where('is_default', true);
                })
                ->where('users.deleted_at', null)
                ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
                ->orderBy('users.updated_at', 'DESC')
                ->paginate(100)->appends($request->query());;

            $roomCasts = DB::table('rooms')->where('is_active', true)
                ->where('rooms.type', RoomType::SYSTEM)
                ->join('users', function ($j) {
                    $j->on('rooms.owner_id', '=', 'users.id')
                        ->where('users.type', UserType::CAST);
                })
                ->leftJoin('avatars', function ($j) {
                    $j->on('avatars.user_id', '=', 'users.id')
                        ->where('is_default', true);
                })
                ->where('users.deleted_at', null)
                ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
                ->orderBy('users.updated_at', 'DESC')
                ->paginate(100)->appends($request->query());;

            $rooms = array_merge($roomGuests->items(), $roomCasts->items());
        }

        return response()->json($rooms, 200);
    }

    public function getRoom(Request $request)
    {
        $id = $request->id;
        $room = DB::table('rooms')->where('is_active', true)
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.id', $id)
            ->join('users', 'rooms.owner_id', '=', 'users.id')
            ->leftJoin('avatars', function ($j) {
                $j->on('avatars.user_id', '=', 'users.id')
                    ->where('is_default', true);
            })
            ->where('users.deleted_at', null)
            ->select('rooms.*', 'users.type As user_type', 'users.gender', 'users.nickname', 'avatars.thumbnail')
            ->orderBy('users.type', 'DESC')->orderBy('users.updated_at', 'DESC')
            ->first();

        return response()->json($room, 200);
    }

    public function getAdminUnreadMessages()
    {
        $messages = DB::table('message_recipient')
            ->where('user_id', 1)
            ->where('is_show', true)
            ->whereNull('read_at')
            ->select('room_id', DB::raw('count(*) as total'))
            ->groupBy('room_id')->get();

        return response()->json($messages, 200);
    }

    public function getRoomsOnWeb(Request $request)
    {
        try {
            $rules = [
                'per_page' => 'numeric|min:1',
                'favorited' => 'boolean',
                'nickname' => 'max:20',
            ];
            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                return $this->respondWithValidationError($validator->errors()->messages());
            }

            $user = $this->guard()->user();
            if (!$user->status) {
                return $this->respondErrorMessage(trans('messages.freezing_account'), 403);
            }

            $unReads = DB::table('message_recipient')
                ->where('user_id', $user->id)
                ->where('is_show', true)
                ->whereNull('read_at')
                ->select('room_id', DB::raw('count(*) as total'))
                ->groupBy('room_id')->get();
            $unReadsMap = [];
            foreach ($unReads as $unRead) {
                $unReadsMap[$unRead->room_id] = $unRead->total;
            }

            $favoritedIds = [];
            $favoritedRooms = [];
            if ($request->favorited) {
                $favoritedIds = $user->favorites()->pluck('favorited_id')->toArray();
                $favoritedRooms = DB::table('favorites')->where('favorites.user_id', $user->id)
                    ->join('room_user', function ($j) use ($favoritedIds) {
                        $j->on('favorites.user_id', '=', 'room_user.user_id');
                    })
                    ->groupBy('room_user.room_id')->get()->reject(function ($item) use ($favoritedIds) {
                        return in_array($item->room_id, $favoritedIds);
                    });

                $favoritedRooms = $favoritedRooms->transform(function ($item) {
                    return $item->room_id;
                })->toArray();
            }

            $rooms = DB::table('room_user')->where('room_user.user_id', $user->id)
                ->leftJoin('rooms', function ($j) use ($favoritedIds) {
                    if (empty($favoritedIds)) {
                        $j->on('rooms.id', '=', 'room_user.room_id')
                            ->where('rooms.is_active', true);
                    } else {
                        $j->on('rooms.id', '=', 'room_user.room_id')
                            ->where('rooms.is_active', true)
                            ->where('rooms.type', RoomType::DIRECT);
                    }
                })
                ->leftJoin('messages', function ($query) use ($favoritedIds) {
                    if (empty($favoritedIds)) {
                        $query->on('room_user.room_id', '=', 'messages.room_id')
                            ->whereRaw('messages.id IN (select MAX(messages.id) from messages join room_user on room_user.room_id = messages.room_id group by room_user.id)');
                    } else {
                        $query->on('room_user.room_id', '=', 'messages.room_id')
                            ->whereRaw('messages.id IN (select MAX(messages.id) from messages join room_user on room_user.room_id = messages.room_id group by room_user.id)')
                            ->where('rooms.type', RoomType::DIRECT);
                    }
                });

            $nickName = $request->nickname;
            if ($nickName) {
                $rooms = $rooms->leftJoin('users', function ($j) use ($nickName) {
                    $j->on('room_user.user_id', '=', 'users.id')
                        ->where('users.nickname', 'like', "%$nickName%");
                });
            }

            $rooms = $rooms->select('rooms.id', 'rooms.order_id', 'rooms.is_active', 'rooms.type as type',
                'messages.message as message', 'messages.image as image', 'messages.image as thumbnail', 'messages.system_type as message_system_type',
                'messages.type as message_type', 'messages.created_at as message_created_at', 'messages.user_id as message_user_id')
                ->orderBy('messages.created_at', 'desc')
                ->groupBy('rooms.id')
                ->paginate(30)->appends($request->query());

            $collection = $rooms->getCollection();
            $roomArray = $collection->pluck('id');
            $users = DB::table('room_user')->whereIn('room_id', $roomArray)
                ->leftJoin('users', 'room_user.user_id', '=', 'users.id')
                ->leftJoin('avatars', function ($j) {
                    $j->on('avatars.user_id', '=', 'users.id')
                        ->where('is_default', true);
                })
                ->select('room_user.room_id', 'users.id', 'users.nickname', 'avatars.thumbnail', 'users.deleted_at')
                ->get();

            $userMap = [];
            foreach ($users as $user) {
                if (!filter_var($user->thumbnail, FILTER_VALIDATE_URL)) {
                    if (!$user->thumbnail) {
                        $user->thumbnail = url('/assets/web/images/gm1/ic_default_avatar@3x.png');
                    } else {
                        $user->thumbnail = Storage::url($user->thumbnail);
                    }
                }
                $user->path = $user->thumbnail;
                $userMap[$user->room_id][] = [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                    'avatars' => [
                        [
                            'path' => $user->path,
                            'thumbnail' => $user->thumbnail
                        ]
                    ],
                    'deleted_at' => $user->deleted_at
                ];
            }

            $collection->transform(function ($room) use ($unReadsMap, $userMap, $users) {
                if (isset($unReadsMap[$room->id])) {
                    $room->unread_count = $unReadsMap[$room->id];
                } else {
                    $room->unread_count = 0;
                }

                if (isset($userMap[$room->id])) {
                    $room->users = array_values(getUniqueArray($userMap[$room->id], 'id'));
                } else {
                    $room->users = [];
                }

                $messageByUser = $users->first(function ($item) use ($room) {
                    unset($item->room_id);
                    return $item->id == $room->message_user_id;
                });
                $room->latest_message = [
                    'type' => $room->message_type,
                    'system_type' => $room->message_system_type,
                    'message' => $room->message,
                    'image' => $room->image,
                    'thumbnail' => $room->thumbnail,
                    'created_at' => $room->message_created_at,
                    'user' => $messageByUser
                ];
                unset($room->message_type);
                unset($room->message_system_type);
                unset($room->thumbnail);
                unset($room->message_created_at);
                unset($room->message);
                unset($room->image);

                return $room;
            });

            if ($request->favorited) {
                $collection = $collection->reject(function ($item) use ($favoritedRooms) {
                    return !in_array($item->id, $favoritedRooms);
                });
                $rooms->setCollection($collection);
            }

            if ('html' == $request->response_type) {
                $rooms = $this->respondWithData($rooms);
                $rooms = $rooms->getData()->data;

                if (!$rooms->data) {
                    return view('web.rooms.no_room');
                }
                return view('web.rooms.content-room', compact('rooms'));
            }

            return $this->respondWithData($rooms);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            dd($e);
        }
    }
}