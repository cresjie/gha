<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventRsvpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if( !Schema::hasTable('event_rsvp') ){

            Schema::create('event_rsvp', function(Blueprint $table){

                $table->engine = 'InnoDB';
                $table->string('event_id')->primary()->comment('also a foreign event.id');
                $table->unsignedInteger('limit')->default(100);
                //$table->boolean('allowed_guest')->default(false);
                
                //allowed_guest was removed in order to save fields
                $table->unsignedInteger('maximum_guest')->default(0)->comment('if 0 this means no guest is allowed');
                $table->boolean('display_remaining')->default(true);
                $table->string('message');
                $table->timestamp('due_date')->nullable();

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
        Schema::drop('event_rsvp');
    }
}
