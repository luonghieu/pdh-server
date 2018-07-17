<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Services\LogService;

class CastRanking extends Command
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
        try{
            \App\CastRanking::truncate();
            $users = User::select('id', 'point')
                ->orderBy('point', 'desc')
                ->orderBy('created_at', 'asc')
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
            LogService::writeErrorLog($e);
        }
    }
}
