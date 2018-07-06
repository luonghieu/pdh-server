<?php

use Faker\Generator as Faker;

$faker = \Faker\Factory::create();
$images = [];
for ($i = 0; $i < 5; $i++) {
    $images[] = generateStorageImage($faker);
}

$factory->define(App\Cast::class, function (Faker $faker) use ($images) {
    return [
        'email' => $faker->unique()->email,
        'facebook_id' => '123',
        'password' => bcrypt('123456789'),
        'fullname' => $faker->name,
        'nickname' => 'suzuka',
        'dob' => $faker->dateTimeThisCentury('-20 years'),
        'gender' => rand(1, 2),
        'avatar' => $faker->randomElement($images),
        'sub_avatar' => $faker->randomElement($images) . ',' . $faker->randomElement($images),
        'thumbnail' => 'abc',
        'info' => 'abc',
        'body_type' => 1,
        'address' => $faker->address,
        'hometown' => $faker->address,
        'current_job' => 'cast',
        'hobbies' => 'music',
        'drink_volume' => 1,
        'living_with' => 'mom',
        'smoke' => rand(true, false),
        'height' => '123',
        'rank' => rand(1, 3),
        'point' => rand(10000, 100000),
        'type' => 2,
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ];
});
