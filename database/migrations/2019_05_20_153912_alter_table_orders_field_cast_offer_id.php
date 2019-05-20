<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersFieldCastOfferId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->integer('cast_offer_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('cast_offer_id');
        });
    }
}
