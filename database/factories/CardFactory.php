<?php

use App\User;
use Faker\Generator as Faker;

$faker = Faker\Factory::create();
$users = User::all()->pluck('id')->toArray();

$factory->define(App\Card::class, function (Faker $faker) use ($users, $faker) {
    return [
        // 'user_id' => $faker->randomElement($users),
        // 'card_id' => '00124587',
        // 'address_city' => $faker->address,
        // 'address_country' => $faker->address,
        // 'address_line1' => $faker->address,
        // 'address_line1_check' => $faker->address,
        // 'address_line2' => $faker->address,
        // 'address_state' => $faker->address,
        // 'address_zip' => $faker->postCode,
        // 'address_zip_check' => $faker->postCode,
        // 'brand' => $faker->postCode,
        // 'country' => $faker->address,
        // 'customer' => $faker->name,
        // 'cvc_check' => rand(100, 999),
        // 'dynamic_last4' =>
        // $table->string('exp_month', 2)->nullable();
        // $table->string('exp_year', 4)->nullable();
        // $table->string('fingerprint')->nullable();
        // $table->string('funding')->nullable();
        // $table->string('last4', 4)->nullable();
        // $table->string('name')->nullable();
        // $table->string('tokenization_method')->nullable();
        // $table->boolean('is_default')->default(false);
        // $table->timestamps();,
    ];
});
