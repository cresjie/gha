<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('user',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary();
			$table->string('email',80)->unique();
			$table->string('password');
			$table->string('password_reminder',500);
			$table->string('user_slug',50)->unique();
			$table->string('first_name',50);
			$table->string('middle_name',50);
			$table->string('last_name',50);
			$table->string('suffix',10);
			$table->string('gender',10);
			$table->string('profile_img')->default('user-default.png');
			$table->boolean('verified')->default(false);

			$table->timestamps();
			$table->rememberToken();

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
		Schema::drop('user');
	}

}
