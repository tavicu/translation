<?php

use Illuminate\Database\Migrations\Migration;

class CreateLanguageEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_entries', function($table)
		{
			$table->increments('id');
			$table->integer('language_id')->unsigned();
			$table->string('namespace', 150)->default('*');
			$table->string('group', 150);
			$table->string('item', 150);
			$table->text('text');
			$table->boolean('unstable')->default('0');
			$table->boolean('locked')->default(0);
			$table->timestamps();

			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			$table->foreign('language_id')->references('id')->on('languages');
			$table->unique(array('language_id', 'namespace', 'group', 'item'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_entries');
	}

}