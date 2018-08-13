<?php

namespace App\Console\Commands;

use App\Enums\PointType;
use App\Point;
use App\Services\LogService;
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

        $points = Point::whereIn('type', [PointType::BUY, PointType::AUTO_CHARGE])
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H") = "' . $dateTime . '"')
            ->where('balance', '>', 0)
            ->get();

        if ($points->count()) {
            try {
                foreach ($points as $point) {
                    $pointUnused = new Point;
                    $pointUnused->point = $point->balance;
                    $pointUnused->balance = $point->balance;
                    $pointUnused->user_id = $point->user_id;
                    $pointUnused->type = PointType::EVICT;
                    $pointUnused->save();

                    $pointAdmin = new Point;
                    $pointAdmin->point = $point->balance;
                    $pointAdmin->balance = $point->balance;
                    $pointAdmin->user_id = 1;
                    $pointAdmin->type = PointType::RECEIVE;
                    $pointAdmin->save();

                    $point->balance = 0;
                    $point->save();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                LogService::writeErrorLog($e);
            }
        }
    }
}
