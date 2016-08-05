<?php

use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class HopperUserTableSeeder
 */
class HopperUserTableSeeder extends Seeder
{
    public function run()
    {
        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        if (env('DB_CONNECTION') == 'mysql') {
            DB::table(config('access.users_table'))->truncate();
        } elseif (env('DB_CONNECTION') == 'sqlite') {
            DB::statement('DELETE FROM ' . config('access.users_table'));
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE ' . config('access.users_table') . ' CASCADE');
        }

        //Add the master administrator, user id of 1
        $users = [
            [
                'name'              => env('HOPPER_MASTER_USER', 'David Alberts'),
                'email'             => env('HOPPER_MASTER_EMAIL', 'dave@lightsourcecreative.com'),
                'password'          => bcrypt(env('HOPPER_MASTER_PASS', '173a0cf52f87115284b9afd84b464762')),
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed'         => true,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];
		
		
		
		if(config('hopper.seed_additional_users', false)){
			$additional_users = [
				[
					'name'              => 'Check In Station 1',
					'email'             => 'checkin1@lightsourecreative.com',
					'password'          => bcrypt('1234'),
					'confirmation_code' => md5(uniqid(mt_rand(), true)),
					'confirmed'         => true,
					'created_at'        => Carbon::now(),
					'updated_at'        => Carbon::now(),
				],
				[
					'name'              => 'Runner',
					'email'             => 'runner@lightsourecreative.com',
					'password'          => bcrypt('1234'),
					'confirmation_code' => md5(uniqid(mt_rand(), true)),
					'confirmed'         => true,
					'created_at'        => Carbon::now(),
					'updated_at'        => Carbon::now(),
				],
				[
					'name'              => 'Graphic Operator',
					'email'             => 'go@lightsourecreative.com',
					'password'          => bcrypt('1234'),
					'confirmation_code' => md5(uniqid(mt_rand(), true)),
					'confirmed'         => true,
					'created_at'        => Carbon::now(),
					'updated_at'        => Carbon::now(),
				]
			];
			$users = array_merge($users, $additional_users);
		}

        DB::table(config('access.users_table'))->insert($users);

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}