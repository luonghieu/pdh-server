<?php
$factory->define(App\Salary::class, function () {
    $salaries = array('非公開',
        '200万未満',
        '200万～400万',
        '400万～600万',
        '600万~800万',
        '800万~1000万',
        '1000万~1500万',
        '1500万~2000万',
        '2000万~3000万',
        '3000万~4000万',
        '4000万~5000万',
        '5000万以上');
    $mumbera = mt_rand(0,11);
        return [
            'name' => $salaries[$mumbera]
            ];
});
