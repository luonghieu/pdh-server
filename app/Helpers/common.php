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
        if (0 === $height) {
            return '非公開';
        }

        return $height;
    }
}

if (!function_exists('latestOnlineStatus')) {
    function latestOnlineStatus($previousTime)
    {
        Carbon\Carbon::setLocale('ja');
        $now = Carbon\Carbon::now();
        $previousTime = Carbon\Carbon::parse($previousTime);
        $divTime = $now->diffForHumans($previousTime);

        return $divTime;
    }
}

if (!function_exists('getImages')) {
    function getImages($path)
    {
        return Storage::url($path);
    }
}
