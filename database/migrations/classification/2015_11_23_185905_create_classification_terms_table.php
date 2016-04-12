<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationTermsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if( !Schema::hasTable('classification_terms') ){
			Schema::create('classification_terms', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->increments('id');
				$table->unsignedInteger('taxonomy_id');
				$table->string('name');
				$table->string('description');
				$table->string('term_code');
				$table->unsignedInteger('order')->default(0);
				$table->unsignedInteger('parent')->default(0);

				$table->foreign('taxonomy_id')
					->references('id')
					->on('classification_term_taxonomy');
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
		Schema::drop('classification_terms');
	}

}
