<?php

namespace App\Http\Controllers\Admin\NotificationSchedule;

use App\Enums\DeviceType;
use App\Enums\NotificationScheduleStatus;
use App\Http\Controllers\Controller;
use App\NotificationSchedule;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationScheduleController extends Controller
{
    public function create()
    {
        $type = request()->type;

        $notificationScheduleStatus = NotificationScheduleStatus::toSelectArray();
        $notificationScheduleDeviceType = DeviceType::toSelectArray();

        return view('admin.notification_schedules.create', compact('notificationScheduleStatus', 'type',
            'notificationScheduleDeviceType'));
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'send_date' => 'required|date|after_or_equal:now',
                'title' => 'required|string',
                'content' => 'required',
                'type' => 'required|numeric',
                'status' => 'required|numeric|regex:/^[1-3]+$/',
                'device_type' => 'required'
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors());
            }

            $notificationSchedule = new NotificationSchedule;

            $input = [
                'send_date' => Carbon::parse($request->send_date),
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'status' => $request->status,
                'device_type' => $request->device_type
            ];

            $notificationSchedule->create($input);

            return redirect('admin/notification_schedules?type=' . $request->type);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return back();
        }
    }

    public function edit($id)
    {
        $type = request()->type;

        $notificationScheduleStatus = NotificationScheduleStatus::toSelectArray();
        $notificationScheduleDeviceType = DeviceType::toSelectArray();

        $notificationSchedule = NotificationSchedule::findOrFail($id);

        return view('admin.notification_schedules.edit', compact(
            'notificationSchedule', 'notificationScheduleStatus', 'type', 'notificationScheduleDeviceType')
        );
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'send_date' => 'required|date|after_or_equal:now',
                'title' => 'required|string',
                'content' => 'required',
                'type' => 'required|numeric',
                'status' => 'required|numeric|regex:/^[1-3]+$/',
                'device_type' => 'integer'
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors());
            }

            $notificationSchedule = NotificationSchedule::findOrFail($id);

            $input = [
                'send_date' => Carbon::parse($request->send_date),
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'status' => $request->status,
                'device_type' => $request->device_type
            ];

            $notificationSchedule->update($input);

            return redirect('admin/notification_schedules?type=' . $request->type);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            return back();
        }
    }

    public function delete($id)
    {
        $notificationSchedule = NotificationSchedule::findOrFail($id);
        $notificationSchedule->delete();

        return redirect('admin/notification_schedules?type=' . request()->type);
    }

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
