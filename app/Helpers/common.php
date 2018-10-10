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

if (!function_exists('getPrefectureName')) {
    function getPrefectureName($id)
    {
        return App\Prefecture::find($id)->name;
    }
}

if (!function_exists('getDay')) {
    function getDay($data = null)
    {
        $date = \Carbon\Carbon::now();
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'];

        if (!$data['month']) {
            $data['month'] = $date->month;
        }

        $number = cal_days_in_month(CAL_GREGORIAN, $data['month'], $date->year);
        $days = [];
        foreach (range(01, $number) as $val) {
            $days[$val] = $val . '日' . '(' . $dayOfWeek[Carbon\Carbon::parse($date->year . '-' . $data['month'] . '-' . $val)->dayOfWeek] . ')';
        }

        return $days;
    }
}

if (!function_exists('dayOfWeek')) {
    function dayOfWeek()
    {
        return ['日', '月', '火', '水', '木', '金', '土'];
    }
}