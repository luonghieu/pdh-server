<?php

if (!function_exists('generateStorageImage')) {
    function generateStorageImage($faker)
    {
        if (env('FILESYSTEM_DRIVER') != 's3') {
            $image = $faker->image(storage_path('app/public'), 400, 400, null, false);
        } else {
            $imageUrl = $faker->imageUrl(400, 400);
            $imageName = Webpatser\Uuid\Uuid::generate()->string . '.jpg';
            Storage::put($imageName, file_get_contents($imageUrl), 'public');

            $image = $imageName;
        }

        return $image;
    }
}

if (!function_exists('getUserHeight')) {
    function getUserHeight($height)
    {
        if (0 == $height) {
            return '非公開';
        }

        return $height;
    }
}

if (!function_exists('online')) {
    function online($previousTime)
    {
        $now = Carbon\Carbon::now();
        $previousTime = Carbon\Carbon::parse($previousTime);
        $time = $now->diffInMinutes($previousTime);

        if ($time <= 1) {
            return 'オンライン中';
        } elseif ($time > 1 && $time <= 1440) {
            return '24時間以内';
        }

        return '2日以内';
    }
}
