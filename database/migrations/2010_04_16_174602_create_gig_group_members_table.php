<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGigGroupMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('gig_group_members',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary();
			$table->string('group_id',80);
			$table->string('user_id',80);
			$table->boolean('is_admin')->default(false);
			$table->boolean('pending')->default(false);
			$table->string('approved_by',80)->nullable();
			$table->string('added_by',80)->nullable();

			$table->timestamp('added_at');
			$table->timestamp('updated_at');
			$table->timestamp('approved_at')->nullable();

			$table->foreign('group_id')->references('id')->on('gig_group');
			$table->foreign('user_id')->references('id')->on('user');
			$table->foreign('approved_by')->references('id')->on('user');
			$table->foreign('added_by')->references('id')->on('user');

			//unique key user_id and group_id, => in order to avoid duplicates in a group
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
		Schema::drop('gig_group_members');
	}

}
