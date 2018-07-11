<?php
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
        DB::table('users')->truncate();

        $faker = Faker\Factory::create();

        // create an admin
        $admin = [
            'email' => 'admin@cheers.dev',
            'password' => bcrypt('123123123'),
            'fullname' => $faker->name,
            'type' => 3,
        ];
        DB::table('users')->insert($admin);

        factory(App\User::class, 15)->create();
        factory(App\Cast::class, 15)->create();
    }
}
