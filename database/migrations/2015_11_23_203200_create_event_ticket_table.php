<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTicketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if( !Schema::hasTable('event_ticket') ){

			Schema::create('event_ticket', function(Blueprint $table){

				$table->engine = 'InnoDB';
				$table->string('id',80)->primary();
				$table->string('event_id',80);
				$table->string('type',20)->comment('free, paid, donation');
				$table->string('name');
				$table->text('description');

				$table->timestamp('sales_start')->nullable()->default(null);
				$table->timestamp('sales_end')->nullable()->default(null);
				$table->decimal('price')->nullable()->default(null);
				$table->string('currency',10)->default('USD');

				$table->unsignedInteger('stock')->default(20);
				$table->integer('minimum')->nullable()->default(1);
				$table->integer('maximum')->default(null)->nullable();

				$table->integer('sort_number')->default(1)->comment('sort number');

				$table->string('added_by')->nullable();
				$table->string('updated_by')->nullable();
				
				$table->softDeletes();
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
		Schema::drop('event_ticket');
	}

}
