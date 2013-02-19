<?php

use Illuminate\Database\Migrations\Migration;

class CreateModeration extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('moderation', function($t)
		{
			$t->increments('id');
			$t->integer('user_id')->nullable();
			$t->integer('character_id')->nullable();
			$t->string('type', 100)->nullable();
			$t->datetime('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('moderation');
	}
}
