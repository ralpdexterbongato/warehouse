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

        // for ($i=0; $i <1 ; $i++) {
        //    $datetime=Carbon::now();
        //    $ACode='150-150-'.str_random(3);
        //    $ICode='L-'.str_random(3);
        //   DB::table('MaterialsTicketDetails')->insert([
        //       'ItemCode' =>$ICode,
        //       'MTType' =>'RR',
        //       'MTNo'=>'NEW',
        //       'AccountCode'=>$ACode,
        //       'UnitCost'=>'5000.54',
        //       'Quantity'=>'8000',
        //       'Amount'=>'40004320',
        //       'CurrentCost'=>'5000.54',
        //       'CurrentQuantity'=>'8000',
        //       'CurrentAmount'=>'40004320',
        //       'MTDate'=>$datetime,
        //   ]);
        //   DB::table('MasterItems')->insert([
        //     'AccountCode'=>$ACode,
        //     'Description'=>'Description'.str_random(20),
        //     'Unit'=>'PC',
        //     'ItemCode'=>$ICode,
        //   ]);

                           //
                            DB::table('users')->insert([
                              'Fname'=>'John',
                              'Lname'=>'Bravo',
                              'Role'=>'0',
                              'Position'=>'ISD Manager',
                              'Username'=>'manager',
                              'password'=>bcrypt('manager'),
                              'Signature'=>'1504577809_59ae0911112f8.png',
                            ]);
                           //
                               DB::table('users')->insert([
                                 'Fname'=>'Engr. Dino Nicolas',
                                 'Lname'=>'Roxas',
                                 'Role'=>'2',
                                 'Position'=>'General Manager',
                                 'Username'=>'gm',
                                 'password'=>bcrypt('gm'),
                                 'Signature'=>'1504583314_59ae1e92d9b84.png',
                               ]);
                           //
                              DB::table('users')->insert([
                                'Fname'=>'Felicisimo',
                                'Lname'=>'Canoñes',
                                'Role'=>'4',
                                'Position'=>'HEAD-Warehouse Section',
                                'Username'=>'warehouse',
                                'password'=>bcrypt('warehouse'),
                                'Signature'=>'1504591711_59ae3f5fd1462.png',
                              ]);
                           //
                            DB::table('users')->insert([
                              'Fname'=>'AuditorFname',
                              'Lname'=>'AuditorLname',
                              'Role'=>'5',
                              'Position'=>'Senior Auditor',
                              'Username'=>'auditor',
                              'password'=>bcrypt('auditor'),
                              'Signature'=>'1504591745_59ae3f81eb25d.png',
                            ]);
                           //
                            DB::table('users')->insert([
                              'Fname'=>'SclerkFname',
                              'Lname'=>'SclerkLname',
                              'Role'=>'6',
                              'Position'=>'Stock Clerk',
                              'Username'=>'clerk',
                              'password'=>bcrypt('clerk'),
                              'Signature'=>'1504591745_59ae3f81eb25d.png',
                            ]);
                           //
                            DB::table('users')->insert([
                              'Fname'=>'BudgetoffName',
                              'Lname'=>'BudgetoffLname',
                              'Role'=>'7',
                              'Position'=>'Budget Officer',
                              'Username'=>'budget',
                              'password'=>bcrypt('budget'),
                              'Signature'=>'1504591756_59ae3f8c86e84.png',
                            ]);
                           DB::table('users')->insert([
                             'Fname'=>'AdminFname',
                             'Lname'=>'AdminLname',
                             'Role'=>'1',
                             'Position'=>'AdminIT',
                             'Username'=>'AdminIT',
                             'password'=>bcrypt('adminIT'),
                             'Signature'=>'1504591765_59ae3f95326e7.png',
                           ]);
        }
    // }
}
