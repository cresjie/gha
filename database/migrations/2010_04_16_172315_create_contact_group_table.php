<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('contact_group',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary();
			$table->string('user_id',80);
			$table->string('group_name');
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('user');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('contact_group');
	}

}
