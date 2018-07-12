<?php

namespace App\Traits;

use Carbon\Carbon;

trait ResourceResponse
{
    public function filterNull($input)
    {
        array_walk_recursive($input, function (&$item, $key) {
            $item = !is_null($item) ? $item : '';

            if ($item instanceof Carbon) {
                $item = $item->toDateTimeString();
            }
        });

        return $input;
    }
}
