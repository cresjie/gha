<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeolocationRelationshipTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if( !Schema::hasTable('geolocation_relationship') )
		{
			Schema::create('geolocation_relationship', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->string('id')->primary();
				$table->string('trackable_id');
				$table->string('trackable_name')->comment("object's full class with namespace");

				$table->string('location');
				$table->string('locality');
				$table->string('administrative_area_level_1');
				$table->string('country');
				$table->string('coordinates');

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
		Schema::drop('geolocation_relationship');
	}

}
