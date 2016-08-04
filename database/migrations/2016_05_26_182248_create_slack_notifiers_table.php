<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlackNotifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slack_notifiers', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name')->nullable();
			$table->string('type')->nullable();
			$table->string('username')->nullable();
			$table->string('channel')->nullable();
			$table->string('pretext')->nullable();
			$table->string('text')->nullable();
			$table->string('color')->nullable();
			$table->string('fields')->nullable();
			$table->boolean('link_names')->default(false);
			$table->boolean('unfurl_links')->default(false);
			$table->boolean('unfurl_media')->default(true);
			$table->boolean('allow_markdown')->default(true);
			$table->string('markdown_in_attachments')->nullable();
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
        Schema::drop('slack_notifiers');
    }
}
