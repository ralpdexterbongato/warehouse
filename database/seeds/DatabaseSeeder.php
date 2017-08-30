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

        for ($i=0; $i <1 ; $i++) {
           $datetime=Carbon::now();
           $ACode='150-150-'.str_random(3);
           $ICode='L-'.str_random(3);
           DB::table('MaterialsTicketDetails')->insert([
               'ItemCode' =>$ICode,
               'MTType' =>'RR',
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
             'Description'=>'Description'.str_random(20),
             'Unit'=>'PC',
             'UnitCost'=>'5000.54',
             'Quantity'=>'8000',
             'Month'=>'Jul',
             'ItemCode_id'=>$ICode,
           ]);


                           DB::table('users')->insert([
                             'Fname'=>'Manager',
                             'Lname'=>'Manager'.str_random(3),
                             'Role'=>'0',
                             'Position'=>'ISD Manager',
                             'Username'=>'manager',
                             'password'=>bcrypt('manager'),
                             'Signature'=>'5J94ys7JwAMbQ70Dj8KwtW4u867RR7UAbutNUReB.png',
                           ]);

                              DB::table('users')->insert([
                                'Fname'=>'GManager',
                                'Lname'=>'GManager',
                                'Role'=>'2',
                                'Position'=>'General Manager',
                                'Username'=>'gm',
                                'password'=>bcrypt('gm'),
                                'Signature'=>'GevZ3OXBg1LdGEuVlDF1PZrXXpbcL4Rv5bkcYinh.png',
                              ]);

                             DB::table('users')->insert([
                               'Fname'=>'Warehouse',
                               'Lname'=>'Man',
                               'Role'=>'4',
                               'Position'=>'Warehouse Head',
                               'Username'=>'warehouse',
                               'password'=>bcrypt('warehouse'),
                               'Signature'=>'sQCtQrcCGVPOSxPwXrugPZEhxt2jT9RdtcZlWclA.png',
                             ]);

                           DB::table('users')->insert([
                             'Fname'=>'Auditor',
                             'Lname'=>'Audit',
                             'Role'=>'5',
                             'Position'=>'Senior Auditor',
                             'Username'=>'auditor',
                             'password'=>bcrypt('auditor'),
                             'Signature'=>'5Rio7kXbQArMeiu8WCzZTVTL5SrCWjSrUWoq5R4y.png',
                           ]);

                           DB::table('users')->insert([
                             'Fname'=>'clerk',
                             'Lname'=>'clerk',
                             'Role'=>'6',
                             'Position'=>'Stock Clerk',
                             'Username'=>'clerk',
                             'password'=>bcrypt('clerk'),
                             'Signature'=>'6nf6IXXOO9aiO7eXIeUjeuLO9ghqvoHSTVkqNqHn.png',
                           ]);

                           DB::table('users')->insert([
                             'Fname'=>'Budget',
                             'Lname'=>'off',
                             'Role'=>'7',
                             'Position'=>'Budget Officer',
                             'Username'=>'budget',
                             'password'=>bcrypt('budget'),
                             'Signature'=>'7D3xKEwCTnrztQvMKSH7RQ1R5oVF1hU5JlwKis3y.png',
                           ]);
                          DB::table('users')->insert([
                            'Fname'=>'ADMINA',
                            'Lname'=>'ADMINO',
                            'Role'=>'1',
                            'Position'=>'Admin',
                            'Username'=>'Admin',
                            'password'=>bcrypt('admin'),
                            'Signature'=>'uvQTEsvolB49LGBlpvnXTO4hI0rVUgQ8Mn6jrOIG.png',
                          ]);
        }
    }
}
