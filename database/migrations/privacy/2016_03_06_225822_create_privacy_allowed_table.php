<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivacyAllowedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('privacy_allowed') ) {
            Schema::create('privacy_allowed', function(Blueprint $table){

                $table->string('id')->primary();

                $table->string('allowable_id');
                $table->string('allowable_name');
                $table->string('allowed_user');

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
        Schema::drop('privacy_allowed');
    }
}
