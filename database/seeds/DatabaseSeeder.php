<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->insert([
        'FullName'=>'John Bravo',
        'Role'=>'0',
        'Position'=>'ISD Manager',
        'Username'=>'manager',
        'password'=>bcrypt('manager'),
        'Signature'=>'1504577809_59ae0911112f8.png',
      ]);
    }
    // }
}
