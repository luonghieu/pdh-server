<?php

namespace App\Console\Commands;

use App\Enums\PointType;
use App\Point;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class DeleteUnusedPointAfter180Days extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:delete_unused_point_after_180_days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unused point after 180 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dateTime = Carbon::now()->subDays(180)->format('Y-m-d H');
        $points = Point::where(function ($query) {
            $query->whereIn('type',
                [
                    PointType::BUY,
                    PointType::AUTO_CHARGE,
                    PointType::INVITE_CODE, 
                    PointType::DIRECT_TRANSFER,
                ])
                ->orWhere(function ($subQ) {
                    $subQ->where('type', PointType::ADJUSTED)
                        ->where('is_cast_adjusted', false)
                        ->where('point', '>=', 0);
                });
            })
            ->where(\DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H") '), '<=', $dateTime)
            ->where('balance', '>', 0);

        try {
            foreach ($points->cursor() as $point) {
                DB::beginTransaction();

                $balancePoint = $point->balance;
                $data = [
                    'point' => -$balancePoint,
                    'balance' => $point->user ? ($point->user->point - $balancePoint) : 0,
                    'user_id' => $point->user_id,
                    'type' => PointType::EVICT,
                ];

                if ($point->invite_code_history_id) {
                    $data['invite_code_history_id'] = $point->invite_code_history_id;
                }

                if ($point->order_id) {
                    $data['order_id'] = $point->order_id;
                }

                $pointUnused = new Point;
                $pointUnused->createPoint($data, true);

                $admin = User::find(1);
                if ($admin->is_admin) {
                    $data['user_id'] = 1;
                    $data['type'] = PointType::RECEIVE;
                    $data['point'] = $point->balance;
                    $data['balance'] = $admin->point + $balancePoint;

                    $pointAdmin = new Point;
                    $pointAdmin->createPoint($data, true);

                    $admin->point += $balancePoint;

                    $admin->save();
                }

                $point->balance = 0;
                $point->save();

                if ($point->user) {
                    $user = User::withTrashed()->find($point->user->id);
                    $user->point -= $balancePoint;
                    $user->save();
                }

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);
        }
    }
}
