<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('facebook_id')->nullable();
            $table->string('password')->nullable();
            $table->string('nickname')->nullable();
            $table->string('fullname')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('avatar')->nullable();
            $table->string('thumbnail')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->unsignedInteger('prefecture_id')->nullable();
            $table->tinyInteger('rank')->default(1);
            $table->integer('point')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
