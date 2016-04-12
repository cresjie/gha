<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('event_date') ){
            Schema::create('event_date', function(Blueprint $table){

                $table->engine = 'InnoDB';
                $table->string('id')->primary();
                $table->string('event_id');

                $table->timestamp('start')->nullable()->default(null);
                $table->timestamp('end')->nullable()->default(null);
                $table->string('timezone');

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
        Schema::drop('event_date');
    }
}
