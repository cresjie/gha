<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGigEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//

		if( !Schema::hasTable('gig_event') )
		{
			Schema::create('gig_event', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->string('id')->primary();
				$table->string('meta_id')->comment('owner of the event');
				$table->string('meta_type');
				$table->string('title');
				$table->string('slug');
				$table->string('slogan');
				$table->text('description')->comment('html description content');
				$table->text('private_description')->comment('html description content');
				$table->string('poster');
				$table->string('location',300);
				$table->string('requisite');


				//plan to be remove
				/*
				$table->timestamp('start')->default(null);
				$table->timestamp('end')->default(null);
				$table->string('timezone'); 
				*/
				
				$table->string('paypal_email');
				$table->string('privacy');
				$table->string('created_by')->comment('meta_id & created_by is almost the same, created_by refers to the user, while meta_id might refer to group');
				$table->boolean('publish')->default(false);

				$table->softDeletes()
				$table->timestamps();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('gig_event');
	}

}
