<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSharestatusesToEventSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_sessions', function (Blueprint $table) {
            //
			$table->string('share_internal', 10)->default("NO");
            $table->string('share_external', 10)->default("NO");
            $table->string('share_recording_internal', 10)->default("NO");
            $table->string('share_recording_external', 10)->default("NO");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
			Schema::table('event_sessions', function (Blueprint $table) {
				$table->dropColumn(['share_internal','share_external','share_recording_internal','share_recording_external']);
			});
    }
}
