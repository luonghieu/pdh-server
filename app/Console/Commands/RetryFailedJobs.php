<?php

namespace App\Console\Commands;

use App\FailedJob;
use Illuminate\Console\Command;
use Laravel\Horizon\Jobs\RetryFailedJob;

class RetryFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:retry_failed_jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry failed jobs';

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
        $failedAt = now()->subMinutes(3);

        $jobs = FailedJob::where('failed_at', '>=', $failedAt);

        foreach ($jobs->cursor() as $job) {
            dispatch(new RetryFailedJob($job->id));
        }
    }
}
