<?php

use Illuminate\Database\Seeder;
use App\Models\Access\Role\Role;
use Illuminate\Support\Facades\DB;

/**
 * Class PermissionRoleSeeder
 */
class HopperPermissionRoleSeeder extends Seeder
{
	public function run()
	{
		if (DB::connection()->getDriverName() == 'mysql') {
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		}

		if (DB::connection()->getDriverName() == 'mysql') {
			DB::table(config('access.permission_role_table'))->truncate();
		} elseif (DB::connection()->getDriverName() == 'sqlite') {
			DB::statement('DELETE FROM ' . config('access.permission_role_table'));
		} else {
			//For PostgreSQL or anything else
			DB::statement('TRUNCATE TABLE ' . config('access.permission_role_table') . ' CASCADE');
		}

		/**
		 * Assign view backend and manage user permissions to checkin in station role
		 */
		Role::find(2)->permissions()->sync([1, 4, 5, 6]);
		Role::find(3)->permissions()->sync([1, 4, 6]);
		Role::find(4)->permissions()->sync([1]);
		/**
		 * 
		 */

		if (DB::connection()->getDriverName() == 'mysql') {
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		}
	}
}