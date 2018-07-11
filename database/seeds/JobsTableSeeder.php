<?php

use Illuminate\Database\Seeder;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jobs')->truncate();

        $data = config('default_data.jobs');
        $data = array_map(function ($name) {
            return compact('name');
        }, $data);

        DB::table('jobs')->insert($data);
    }
}
