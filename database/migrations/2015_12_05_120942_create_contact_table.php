<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('contact') )
        {
            Schema::create('contact', function(Blueprint $table){

                $table->string('id')->primary();
                $table->string('requestor');
                $table->string('user_id');

                $table->boolean('is_confirmed')->default(false);
                $table->timestamp('seen_at')->default(null)->nullable();
                $table->timestamps();
                //$table->softDeletes();
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
        Schema::drop('contact');
    }
}
