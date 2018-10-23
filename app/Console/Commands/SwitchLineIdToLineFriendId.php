<?php

namespace App\Console\Commands;

use App\Cast;
use Illuminate\Console\Command;

class SwitchLineIdToLineFriendId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheers:switch_line_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch line_id to line_friend_id';

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
        $casts = Cast::where('line_id', '<>', null)->get();

        foreach ($casts as $cast) {
            $lineId = $cast->line_id;

            $cast->update(['line_friend_id' => $lineId]);
        }
    }
}
