<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactGroupContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('contact_group_contacts',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary();
			$table->string('group_id',80);
			$table->string('user_id',80);
			$table->string('email',80);

			$table->timestamp('added_at');
			$table->timestamp('updated_at');

			$table->foreign('group_id')->references('id')->on('contact_group');
			$table->foreign('user_id')->references('id')->on('user');
			//$table->foreign('email')->references('email')->on('user');
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
		Schema::drop('contact_group_contacts');
	}

}
