<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGigEventOrganizerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if( !Schema::hasTable('gig_event_organizer') )
		{
			Schema::create('gig_event_organizer', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->string('id')->primary();
				$table->string('event_id');
				$table->string('user_id');
				$table->string('added_by')->nullable()->default(null);
				$table->boolean('is_admin')->default(false);
				$table->boolean('is_publisher')->default(false);
				$table->timestamps();

				$table->foreign('event_id')->references('id')->on('gig_event');
				$table->foreign('user_id')->references('id')->on('user');
				$table->foreign('added_by')->references('id')->on('user');
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
		Schema::drop('gig_event_organizer');
	}

}
