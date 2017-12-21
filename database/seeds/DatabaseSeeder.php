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
        'FullName'=>'Administrator',
        'Role'=>'1',
        'Position'=>'Admin',
        'Username'=>'admin',
        'password'=>bcrypt('admin'),
        'Signature'=>'1504577809_59ae0911112f8.png',
      ]);
    }
    // }
}
