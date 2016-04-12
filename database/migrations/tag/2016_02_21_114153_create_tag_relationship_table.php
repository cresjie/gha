<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('tag_relationship') ){

            Schema::create('tag_relationship', function(Blueprint $table){

                $table->engine = 'InnoDB';
                $table->string('id')->primary();
                $table->string('taggable_id');
                $table->string('taggable_type');
                
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
        Schema::drop('tag_relationship');
    }
}
