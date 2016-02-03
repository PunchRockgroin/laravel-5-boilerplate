<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('session_id')->unique();
            $table->boolean('checked_in')->default(false);
            $table->string('speakers')->nullable();
            $table->string('onsite_phone')->nullable();
            $table->string('presentation_owner')->nullable();
            $table->dateTime('check_in_datetime')->nullable();
            $table->string('approval_brand', 10)->default("NO");
            $table->string('approval_revrec', 10)->default("N/A");
            $table->string('approval_legal', 10)->default("N/A");
            $table->text('dates_rooms')->nullable();
            $table->text('history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_sessions');
    }
}
