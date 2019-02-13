<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\RankSchedule;
use App\Services\LogService;
use Illuminate\Http\Request;

class RankScheduleController extends Controller
{
    public function getRankSchedule()
    {
        $rankSchedule = RankSchedule::first();

        return view('admin.rank_schedules.index', compact('rankSchedule'));
    }

    public function setRankSchedule(Request $request)
    {
        try {
            $rankSchedule = RankSchedule::first();

            $input = [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'num_of_attend_platium' => $request->num_of_attend_platium,
                'num_of_avg_rate_platium' => $request->num_of_avg_rate_platium,
                'num_of_attend_up_platium' => $request->num_of_attend_up_platium,
                'num_of_avg_rate_up_platium' => $request->num_of_avg_rate_up_platium,
            ];

            if (!$rankSchedule) {
                $rankSchedule = new RankSchedule;
                $rankSchedule = $rankSchedule->create($input);

                return redirect()->route('admin.rank_schedules.index');
            }

            $rankSchedule->update($input);
            
            return redirect()->route('admin.rank_schedules.index');
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
