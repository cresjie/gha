<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailVerificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('email_verification',function(Blueprint $table){
			$table->engine = 'InnoDB';
			$table->string('user_id',80)->primary();
			$table->string('verification_token');

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
		Schema::drop('email_verification');
	}

}
