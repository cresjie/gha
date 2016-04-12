<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGigGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('gig_group',function(Blueprint $table){

			$table->engine = 'InnoDB';
			$table->string('id',80)->primary();
			$table->string('name',100);
			$table->string('cover_img',100);
			$table->string('slug',100);
			$table->string('slogan')''
			$table->text('description');
			$table->string('privacy',20)->default('public');
			$table->string('created_by',80);

			$table->timestamps();
			$table->softDeletes();

			$table->foreign('created_by')->references('id')->on('user');
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
		Schema::drop('gig_group');
	}

}
