<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webpatser\Uuid\Uuid;

class MakeAvatarThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $avatar;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $info = pathinfo($this->avatar->path);
        $contents = file_get_contents($this->avatar->path);
        $imageName = Uuid::generate()->string . '.' . strtolower($info['extension']);
        $image = \Image::make($contents)->resize(200, 200)->encode($info['extension']);

        \Storage::put($imageName, $image->__toString(), 'public');
        $this->avatar->update([
            'thumbnail' => $imageName,
        ]);
    }
}
