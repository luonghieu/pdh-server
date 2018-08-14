<?php

namespace App\Console\Commands;

use App\Enums\PointType;
use App\Point;
use App\Services\LogService;
use Carbon\Carbon;
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

        try {
            foreach (Point::whereIn('type', [PointType::BUY, PointType::AUTO_CHARGE])
                ->whereDate(\DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H") '), '<=', $dateTime)
                ->where('balance', '>', 0)
                ->cursor() as $point) {
                $data = [
                    'point' => $point->balance,
                    'balance' => $point->balance,
                    'user_id' => $point->user_id,
                    'type' => PointType::EVICT,
                ];
                if ($point->order_id) {
                    $data['order_id'] = $point->order_id;
                }
                $pointUnused = new Point;
                $pointUnused->createPoint($data);

                $data['user_id'] = 1;
                $data['type'] = PointType::RECEIVE;

                $pointAdmin = new Point;
                $pointAdmin->createPoint($data);

                $point->balance = 0;
                $point->save();
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
