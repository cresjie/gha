<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupDiscussionReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('group_discussion_reply',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary();
			$table->string('gd_id',80)->comment('group discussion id');
			$table->string('user_id',80);
			$table->text('message');

			$table->timestamps();

			$table->foreign('gd_id')->references('id')->on('group_discussion');
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
		Schema::drop('group_discussion_reply');
	}

}
