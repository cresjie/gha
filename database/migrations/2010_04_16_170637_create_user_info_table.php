<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('user_info',function(Blueprint $table){
			$table->engine = 'InnoDB';
			$table->string('user_id',80)->primary();
			$table->string('telephone',50);
			$table->string('mobile',50);
			$table->string('address');
			$table->string('company');

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
		Schema::drop('user_info');
	}

}
