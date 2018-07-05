<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCastOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cast_order', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cast_id');
            $table->unsignedInteger('order_id');
            $table->timestamps();

            $table->foreign('cast_id')
                ->references('id')
                ->on('users');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_order');
    }
}
