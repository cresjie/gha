<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationTermRelationshipTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if( !Schema::hasTable('classification_term_relationship') ){

			Schema::create('classification_term_relationship', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->string('id')->primary();
				$table->string('term_id');
				$table->string('termable_id')->comment('foreign id');
				$table->string('termable_type')->comment('foreign object classname');
				
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
		Schema::drop('classification_term_relationship');
	}

}
