<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessTableSeeder extends Seeder
{
    public function run()
    {
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

//        $this->call(UserTableSeeder::class);
//        $this->call(RoleTableSeeder::class);
//        $this->call(UserRoleSeeder::class);
//        $this->call(PermissionTableSeeder::class);
//        $this->call(PermissionRoleSeeder::class);
		
		  $this->call(HopperUserTableSeeder::class);
		  $this->call(HopperRoleTableSeeder::class);
          $this->call(HopperUserRoleSeeder::class);
          $this->call(HopperPermissionTableSeeder::class);
          $this->call(HopperPermissionRoleSeeder::class);
		

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    }
}