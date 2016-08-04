<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_session_id')->unsigned()->index()->nullable();
            $table->string('filename');
            $table->string('mime')->nullable();
            $table->string('session_id')->nullable();
            $table->string('storage_disk')->nullable();
            $table->string('path')->nullable();
            $table->string('status')->nullable();
            $table->string('event')->nullable();
            $table->text('data')->nullable();
            $table->text('history')->nullable();
            $table->text('filename_history')->nullable();
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
        Schema::drop('file_entities');
    }
}
