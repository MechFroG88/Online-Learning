<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('class_user_id');
            $table->integer('event_id');
            $table->integer('period_id');
            $table->date('date');
            $table->string('method',50);
            $table->text('link');
            $table->text('streamId');
            $table->text('streamPassword');
            $table->longText('description');
            $table->index('class_user_id');
            $table->index('event_id');
            $table->index('period_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('choices');
    }
}
