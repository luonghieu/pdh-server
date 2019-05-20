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
            $table->integer('prefecture_id')->unsigned()->nullable();
            $table->string('address');
            $table->date('date');
            $table->time('start_time');
            $table->integer('duration');
            $table->integer('cost');
            $table->integer('temp_point');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cast_class_id')->references('id')->on('cast_classes');
            $table->foreign('guest_id')->references('id')->on('users');
            $table->foreign('prefecture_id')->references('id')->on('prefectures');
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
            $table->dropForeign(['order_id']);
            $table->dropForeign(['cast_class_id']);
            $table->dropForeign(['guest_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['prefecture_id']);
        });

        Schema::dropIfExists('cast_offers');
    }
}
