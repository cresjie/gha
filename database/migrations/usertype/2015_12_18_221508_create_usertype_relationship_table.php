<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsertypeRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('usertype_relationship') ){

            Schema::create('usertype_relationship', function(Blueprint $table){

                $table->engine = 'InnoDB';
                $table->string('user_id')->primary();
                $table->unsignedInteger('account_type');
                $table->timestamp('expire_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('user');
                $table->foreign('account_type')->references('id')->on('usertype_account');
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
        Schema::drop('usertype_relationship');
    }
}
