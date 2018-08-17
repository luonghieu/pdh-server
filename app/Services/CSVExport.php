<?php

namespace App\Services;

use DB;
use League\Csv\Writer;
use SplTempFileObject;

class CSVExport
{
    public static function toCSV($input, $header = [], $isRaw = false)
    {
        if ($isRaw) {
            $result = DB::select($input);
            $result = collect($result)->map(function ($x) {return (array) $x;})->toArray();
        } else {
            $result = $input;
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        if ($header) {
            $csv->insertOne($header);
        }

        $csv->insertAll($result);

        return $csv;
    }
}
