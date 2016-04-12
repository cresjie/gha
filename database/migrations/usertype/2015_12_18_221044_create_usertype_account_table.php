<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsertypeAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('usertype_account') ){

            Schema::create('usertype_account', function(Blueprint $table){

                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('name');
                $table->string('code');
                $table->string('description');
                $table->string('currency',10);
                $table->decimal('price');

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
        Schema::drop('usertype_account');
    }
}
