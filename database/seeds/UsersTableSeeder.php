<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        // create an admin
        $admin = [
            'email' => 'admin@cheers.dev',
            'password' => bcrypt('123123123'),
            'fullname' => $faker->name,
            'type' => 3,
        ];
        DB::table('users')->insert($admin);

        // create fake users
        $limit = 50;
        $images = [];
        for ($i = 0; $i < 5; $i++) {
            $images[] = generateStorageImage($faker);
        }

        $datas = [];
        for ($i = 0; $i < $limit; $i++) {
            $datas[] = [
                'email' => $faker->unique()->email,
                'facebook_id' => '123',
                'password' => bcrypt('123456789'),
                'fullname' => $faker->name,
                'nickname' => 'suzuka',
                'date_of_birth' => $faker->dateTimeThisCentury('-20 years'),
                'gender' => rand(1, 2),
                'prefecture_id' => 1,
                'avatar' => $faker->randomElement($images),
                'thumbnail' => 'abc',
                'phone' => '0123456789',
                'rank' => 1,
                'point' => 1,
                'type' => rand(1, 2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('users')->insert($datas);
    }
}
