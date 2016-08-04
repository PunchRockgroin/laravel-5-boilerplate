<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('event_session_id')->unsigned()->index();
            $table->integer('file_entity_id')->unsigned()->index();
            $table->string('session_id')->nullable();
            $table->string('checkin_username')->nullable();
            $table->string('visitors')->nullable();
            $table->string('filename_uploaded')->nullable();
            $table->text('updates_made')->json()->nullable();
            $table->text('checkin_notes')->nullable();
            $table->string('design_username')->nullable();            
            $table->tinyInteger('difficulty')->nullable();
            $table->text('design_notes')->nullable();
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
        Schema::drop('visits');
    }
}
