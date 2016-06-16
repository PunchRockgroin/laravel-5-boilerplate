<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessTableSeeder extends Seeder
{
    public function run()
    {
        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $this->call(HopperUserTableSeeder::class);
        $this->call(HopperRoleTableSeeder::class);
        $this->call(HopperUserRoleSeeder::class);
        $this->call(PermissionGroupTableSeeder::class);
        $this->call(HopperPermissionTableSeeder::class);
        $this->call(PermissionDependencyTableSeeder::class);

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    }
}