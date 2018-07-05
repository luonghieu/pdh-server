<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('pricing_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('address');
            $table->tinyInteger('total_cast');
            $table->tinyInteger('status');

            $table->timestamp('accept_time')->nullable();
            $table->timestamp('cancel_time')->nullable();
            $table->timestamp('actual_start_time')->nullable();
            $table->timestamp('actual_end_time')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('pricing_id')
                ->references('id')
                ->on('pricings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
