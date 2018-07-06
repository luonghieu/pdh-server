<?php

use Faker\Generator as Faker;

$factory->define(App\Prefecture::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'name_kana' => $faker->word,
    ];
});
