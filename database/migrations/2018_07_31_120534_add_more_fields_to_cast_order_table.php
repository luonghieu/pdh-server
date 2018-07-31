<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToCastOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cast_order', function (Blueprint $table) {
            $table->tinyInteger('extra_time')->nullable()->after('point');
            $table->tinyInteger('night_time')->nullable()->after('extra_time');
            $table->tinyInteger('total_time')->nullable()->after('night_time');
            $table->integer('extra_point')->nullable()->after('user_id');
            $table->integer('allowance_point')->nullable()->after('extra_point');
            $table->renameColumn('point', 'total_point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cast_order', function (Blueprint $table) {
            $table->dropColumn('extra_time');
            $table->dropColumn('night_time');
            $table->dropColumn('total_time');
            $table->dropColumn('extra_point');
            $table->dropColumn('allowance_point');
            $table->renameColumn('total_point', 'point');
        });
    }
}
