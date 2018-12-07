<?php

namespace App\Console\Commands;

use App\Cast;
use App\CastRanking;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CastRankingSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:update_cast_ranking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cast ranking';

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
        try {
            $castRanking = CastRanking::first();

            if ($castRanking) {
                if (Carbon::parse($castRanking->created_at)->format('Y/m/d') == Carbon::parse(now())->format('Y/m/d')) {
                    return;
                }
            }

            CastRanking::truncate();
            $users = Cast::select('id', 'total_point')
                ->orderBy('total_point', 'desc')
                ->orderBy('created_at', 'asc')
                ->take(10)
                ->get();
            $ranking = 1;

            foreach ($users as $user) {
                $data[] = [
                    'user_id' => $user->id,
                    'ranking' => $ranking++,
                    'point' => $user->total_point,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            \DB::table('cast_rankings')->insert($data);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
