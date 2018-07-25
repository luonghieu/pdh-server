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
        if (!empty($this->avatar->getOriginal('thumbnail'))) {
            $nameThumbnailOld = $this->avatar->getOriginal('thumbnail');
            \Storage::delete($nameThumbnailOld);
        }

        $info = pathinfo($this->avatar->path);
        $contents = file_get_contents($this->avatar->path);
        $thumbnailName = Uuid::generate()->string . '.' . strtolower($info['extension']);
        $image = \Image::make($contents)->resize(200, 200)->encode($info['extension']);

        \Storage::put($thumbnailName, $image->__toString(), 'public');

        $this->avatar->update([
            'thumbnail' => $thumbnailName,
        ]);
    }
}
