<?php

use Faker\Generator as Faker;

$factory->define(App\Prefecture::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'name_kana' => $faker->name,
    ];
});
