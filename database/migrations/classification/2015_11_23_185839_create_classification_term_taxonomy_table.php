<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationTermTaxonomyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if(!Schema::hasTable('classification_term_taxonomy')){
			
			Schema::create('classification_term_taxonomy', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->increments('id');
				$table->string('title');
				$table->string('description');
				$table->string('taxonomy_code');
				$table->unsignedInteger('parent')->default(0);

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
		Schema::drop('classification_term_taxonomy');
	}

}
