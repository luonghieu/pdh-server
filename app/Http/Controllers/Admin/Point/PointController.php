<?php

namespace App\Http\Controllers\Admin\Point;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Point;
use App\Services\CSVExport;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function getPoin(Request $request)
    {
        $keyword = $request->search_point_type;
        $userTypes = [
            UserType::GUEST => UserType::getDescription(UserType::GUEST),
            UserType::CAST => UserType::getDescription(UserType::CAST),
            3 => '全て', //all
        ];

        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        $users = User::with(['points' => function ($query) use ($fromDate, $toDate) {
            if ($fromDate) {
                $query->where('points.created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $query->where('points.created_at', '<=', $toDate);
            }
        }])
            ->whereHas('points', function ($query) use ($fromDate, $toDate) {
                if ($fromDate) {
                    $query->where('points.created_at', '>=', $fromDate);
                }

                if ($toDate) {
                    $query->where('points.created_at', '<=', $toDate);
                }
            });

        if ($keyword) {
            if (3 != $keyword) {
                $users->where('type', "$keyword");
            }
        }

        $users = $users->orderBy('created_at', 'DESC');
        $pointsExport = $users->get();
        $users = $users->paginate($request->limit ?: 10);

        if ('export' == $request->submit) {
            $data = collect($pointsExport)->map(function ($item) {
                return [
                    $item->id,
                    $item->fullname,
                    UserType::getDescription($item->type),
                    $item->positivePoints($item->points),
                    $item->negativePoints($item->points),
                    $item->totalBalance($item->points),
                ];
            })->toArray();

            $sum = [
                '合計',
                '',
                '',
                $pointsExport->sum(function ($user) {
                    return $user->positivePoints($user->points);
                }),
                $pointsExport->sum(function ($user) {
                    return $user->negativePoints($user->points);
                }),
                $pointsExport->sum(function ($user) {
                    return $user->totalBalance($user->points);
                }),
            ];

            array_push($data, $sum);

            $header = [
                'ユーザーID',
                'ユーザー名',
                'ユーザー種別',
                'ポイントの増加額',
                'ポイントの減少額',
                'ポイントの残高',
            ];
            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);

                $request->session()->flash('msg', trans('messages.server_error'));

                return redirect()->route('admin.points.point_users');
            }

            $file->output('point_user_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }

        return view('admin.points.point_user', compact('users', 'userTypes'));
    }
}
