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

        factory(App\User::class, 50)->create();

        // generate avatars for users
        $users = App\User::all();
        $images = [];
        for ($i = 0; $i < 10; $i++) {
            $images[] = generateStorageImage($faker);
        }

        foreach ($users as $user) {
            $numberOfImages = rand(1, 10);

            for ($j = 0; $j < $numberOfImages; $j++) {
                $user->avatars()->create([
                    'path' => $faker->randomElement($images),
                ]);
            }
        }
    }
}
