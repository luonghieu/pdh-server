<?php

use Illuminate\Database\Seeder;
use App\Salary;

class SalariesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Salary::class, 12)->create();
    }
}