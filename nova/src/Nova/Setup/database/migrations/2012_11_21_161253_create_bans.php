<?php

use Illuminate\Database\Migrations\Migration;

class CreateBans extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bans', function($t)
		{
			$t->increments('id');
			$t->integer('level')->default(1);
			$t->string('ip_address', 16)->nullable();
			$t->string('email', 100)->nullable();
			$t->text('reason')->nullable();
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
		Schema::drop('bans');
	}
}
