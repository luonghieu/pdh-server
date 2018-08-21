<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Prefecture;
use App\Repositories\CastClassRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $castClass;

    public function __construct()
    {
        $this->castClass = app(CastClassRepository::class);
    }

    public function index(Request $request)
    {
        $orderBy = $request->only('id', 'status');
        $keyword = $request->search;

        $users = User::where('type', '<>', UserType::ADMIN);

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $users->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $users->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($request->has('search')) {
            $users->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $users->orderBy($key, $value);
            }
        } else {
            $users->orderBy('created_at', 'DESC');
        }

        $users = $users->paginate($request->limit ?: 10);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $prefectures = Prefecture::whereIn('id', Prefecture::SUPPORTED_IDS)
            ->orderByRaw("FIELD(id, " . implode(',', Prefecture::SUPPORTED_IDS) . " )")
            ->get();

        $castClasses = $this->castClass->all();

        return view('admin.users.show', compact('user', 'castClasses', 'prefectures'));
    }

    public function changeActive(User $user)
    {
        $user->status = !$user->status;

        $user->save();

        return redirect()->route('admin.users.show', ['user' => $user->id]);
    }

    public function changeCastClass(User $user, Request $request)
    {
        $newClass = $this->castClass->find($request->cast_class);

        $user->class_id = $newClass->id;
        $user->cost = $newClass->cost;

        $user->save();

        return redirect()->route('admin.users.show', ['user' => $user->id]);
    }

    public function changePrefecture(User $user, Request $request)
    {
        $newPrefecture = Prefecture::find($request->prefecture);

        if ($newPrefecture) {
            $user->prefecture_id = $newPrefecture->id;
            $user->save();
        }

        return redirect()->route('admin.users.show', ['user' => $user->id]);
    }

    public function registerGuest(User $user)
    {
        $user->type = UserType::GUEST;
        $user->save();

        return redirect(route('admin.users.show', ['user' => $user->id]));
    }
}
