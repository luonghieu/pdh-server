<?php

namespace App\Console\Commands;

use App\Enums\UserType;
use App\Services\LogService;
use App\User;
use Illuminate\Console\Command;

class WorkingToday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:reset_working_today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset working today';

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
            $users = User::select('id', 'type')->get();
            foreach ($users as $user) {
                if (UserType::CAST == $user->type) {
                    \DB::table('users')
                        ->where('id', $user->id)
                        ->update(['working_today' => false]);
                }
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
