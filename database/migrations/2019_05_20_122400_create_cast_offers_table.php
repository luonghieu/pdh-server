<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCastOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cast_offers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('guest_id')->unsigned()->nullable();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('cast_class_id')->unsigned()->nullable();
            $table->string('address');
            $table->date('date');
            $table->time('start_time');
            $table->float('duration');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cast_class_id')->references('id')->on('cast_classes');
            $table->foreign('guest_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cast_offers', function (Blueprint $table) {
            $table->dropForeign(['order_id', 'cast_class_id', 'guest_id', 'user_id']);
        });

        Schema::dropIfExists('cast_offers');
    }
}
