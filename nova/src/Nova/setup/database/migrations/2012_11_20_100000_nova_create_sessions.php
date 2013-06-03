<?php

use Illuminate\Database\Migrations\Migration;

class NovaCreateSessions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if ( ! Schema::hasTable('sessions'))
		{
			Schema::create('sessions', function($t)
			{
				$t->string('id')->unique();
				$t->text('payload');
				$t->integer('last_activity');
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}

}
