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
      $users = array(
          array('FullName' =>'Administrator' ,'Manager'=>'2','Role'=>'1','Position'=>'Admin','Username'=>'admin','password'=>bcrypt('admin'),'Signature'=>'1.png'),
          array('FullName' =>'John Doe' ,'Manager'=>NULL,'Role'=>'0','Position'=>'ITD Manager','Username'=>'manager1','password'=>bcrypt('manager1'),'Signature'=>'2.png'),
          array('FullName' =>'Carlo Joe' ,'Manager'=>NULL,'Role'=>'2','Position'=>'General Manager','Username'=>'generalmanager','password'=>bcrypt('generalmanager'),'Signature'=>'3.png'),
          array('FullName' =>'Rose Foo' ,'Manager'=>'2','Role'=>'3','Position'=>'Warehouse assistant','Username'=>'assistant','password'=>bcrypt('assistant'),'Signature'=>'4.png'),
          array('FullName' =>'Jane Foo' ,'Manager'=>'2','Role'=>'4','Position'=>'Warehouse-Head','Username'=>'warehousehead','password'=>bcrypt('warehousehead'),'Signature'=>'5.png'),
          array('FullName' =>'Sam Brodes' ,'Manager'=>'2','Role'=>'5','Position'=>'Senior Auditor','Username'=>'auditor','password'=>bcrypt('auditor'),'Signature'=>'6.png'),
          array('FullName' =>'Van Joe Doo' ,'Manager'=>'2','Role'=>'6','Position'=>'Stock clerk','Username'=>'clerk','password'=>bcrypt('clerk'),'Signature'=>'7.png'),
          array('FullName' =>'Shaina Coon' ,'Manager'=>'2','Role'=>'7','Position'=>'Budget Officer','Username'=>'budgetofficer','password'=>bcrypt('budgetofficer'),'Signature'=>'8.png')
      );
      DB::table('users')->insert($users);
    }
    // }
}
