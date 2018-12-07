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
        if (null === $previousTime) {
            return '';
        }

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
        $date = \Carbon\Carbon::now()->addMinutes(30);
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

if (!function_exists('removeHtmlTags')) {
    function removeHtmlTags($content)
    {
        $content = str_replace("<br />", PHP_EOL, $content);
        $content = str_replace("&nbsp;", " ", $content);

        return strip_tags($content);
    }
}

if (!function_exists('linkExtractor')) {
    function linkExtractor($html)
    {
        $linkArray = [];
        if (preg_match_all('/<img\s+.*?src=[\"\']?([^\"\' >]*)[\"\']?[^>]*>/i', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                array_push($linkArray, $match[1]);
            }
        }
        return $linkArray;
    }
}

if ( ! function_exists('put_permanent_env'))
{
    function putPermanentEnv($key, $value)
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('='.env($key), '/');

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }
}
