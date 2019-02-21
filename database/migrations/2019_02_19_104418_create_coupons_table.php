<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->integer('point')->nullable();
            $table->integer('time')->nullable();
            $table->string('note')->unique()->nullable();
            $table->boolean('is_filter_after_created_date')->nullable();
            $table->tinyInteger('filter_after_created_date')->nullable();
            $table->boolean('is_filter_order_duration')->nullable();
            $table->float('filter_order_duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
