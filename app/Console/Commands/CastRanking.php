<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CastRanking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'name:cast_ranking';

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
            Log::info("Cast Ranking Begin By Cron Job.");
            dispatch(new \App\Jobs\CastRanking());
        } catch (\Exception $e){
            Log::info('Cron Job Error' . $e->getMessage());
        }
    }
}
