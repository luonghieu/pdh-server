<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Illuminate\Support\Facades\Log;

class CastRanking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            \App\CastRanking::truncate();
            $users = User::select('id', 'point')
                ->orderBy('point', 'desc')
                ->take(10)
                ->get();
            foreach ($users as $user){
                $data[] = [
                    'user_id' => $user->id,
                    'point' => $user->point,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ];
            }
            \DB::table('cast_rankings')->insert($data);
        } catch (\Exception $e){
            Log::info('Cast ranking error '. $e->getMessage());
        }
    }
}
