<?php

namespace App\Http\Controllers\Admin\NotificationSchedule;

use App\Http\Controllers\Controller;
use App\NotificationSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationScheduleController extends Controller
{
    public function getNotificationScheduleList(Request $request)
    {
        $type = $request->type;

        $notificationSchedules = NotificationSchedule::where('type', $type);

        $keyword = $request->search;

        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;

        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        if ($fromDate) {
            $notificationSchedules->where(function ($query) use ($fromDate) {
                $query->where('send_date', '>=', $fromDate);
            });
        }

        if ($toDate) {
            $notificationSchedules->where(function ($query) use ($toDate) {
                $query->where('send_date', '<=', $toDate);
            });
        }

        if ($keyword) {
            $notificationSchedules->where('title', 'like', "%$keyword%");
        }

        $notificationSchedules = $notificationSchedules->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.notification_schedules.index', compact('notificationSchedules', 'type'));
    }
}
