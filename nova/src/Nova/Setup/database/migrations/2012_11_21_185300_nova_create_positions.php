<?php

use Illuminate\Database\Migrations\Migration;

class NovaCreatePositions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up($explicitGenre = false)
	{
		// Get the genre
		$genre = ($explicitGenre) ?: Config::get('nova.genre');

		Schema::create("positions_{$genre}", function($t)
		{
			$t->increments('id');
			$t->string('name');
			$t->text('desc')->nullable();
			$t->integer('dept_id')->unsigned();
			$t->integer('order')->nullable();
			$t->integer('open')->default(1);
			$t->boolean('status')->default(Status::ACTIVE);
			$t->string('type')->default('officer');
			$t->timestamps();
		});

		if ($explicitGenre)
		{
			// Pull in the genre data file
			include SRCPATH."Setup/database/genres/{$genre}.php";

			foreach ($positions as $p)
			{
				PositionModel::create($p);
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down($explicitGenre = false)
	{
		// Get the genre
		$genre = ($explicitGenre) ?: Config::get('nova.genre');

		Schema::drop("positions_{$genre}");
	}

}