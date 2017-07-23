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
        // $this->call(UsersTableSeeder::class);
        //DB::table('MaterialsTicketDetails')->insert(['ItemCode'=>'17-'.str_random(4),MTType =>'NEW',MTNo=>'NEW',AccountCode=>'150'])
        for ($i=0; $i <1000 ; $i++) {
          $datetime=Carbon::now();
          $ACode='150-150-'.str_random(3);
          $ICode='L-'.str_random(3);
          DB::table('MaterialsTicketDetails')->insert([
              'ItemCode' =>$ICode,
              'MTType' =>'NEW',
              'MTNo'=>'NEW',
              'AccountCode'=>$ACode,
              'UnitCost'=>'5000.54',
              'Quantity'=>'8000',
              'Unit'=>'PC',
              'Amount'=>'40004320',
              'CurrentCost'=>'5000.54',
              'CurrentQuantity'=>'8000',
              'CurrentAmount'=>'40004320',
              'MTDate'=>$datetime,
          ]);
          DB::table('MasterItems')->insert([
            'AccountCode'=>$ACode,
            'Description'=>'Description of the item',
            'Unit'=>'PC',
            'UnitCost'=>'5000.54',
            'Quantity'=>'8000',
            'Month'=>'Jul',
            'ItemCode_id'=>$ICode,
          ]);

          //DB::table('users')->insert([
            //'Fname'=>'Ralp',
          //  'Lname'=>str_random(10),
          //  'Role'=>'1',
          //  'Position'=>'Admin',
          //  'Username'=>'admin',
            //'password'=>bcrypt('admin'),
            //'Signature'=>'alksdmaklsmd',
        //  ]);
        }
    }
}
