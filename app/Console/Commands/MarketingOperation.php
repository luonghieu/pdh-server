<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarketingOperation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:marketing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marketing Operation';

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
        $startDate = Carbon::parse('2018-12-06')->startOfDay();
        $endDate = Carbon::parse('2018-12-31')->endOfDay();
        $guests = User::where(function($q) use ($startDate, $endDate) {
            $q->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        })->where('campaign_participated', false)->get();

        \Notification::send($guests, new \App\Notifications\MarketingOperation());
    }
}
