<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupDiscussionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('group_discussion',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary(); //Programmatically generated
			$table->string('group_id',80);
			$table->string('user_id',80)->comment('the use who created the topic');
			$table->string('subject');
			$table->text('message');

			$table->timestamps();

			$table->foreign('group_id')->references('id')->on('gig_group');
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
		Schema::drop('group_discussion');
	}

}
