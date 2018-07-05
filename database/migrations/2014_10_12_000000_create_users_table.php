<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('facebook_id')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('nickname')->nullable();
            $table->string('fullname')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('avatar')->nullable();
            $table->text('sub_avatar')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('info')->nullable();
            $table->integer('height')->nullable();
            $table->string('body_type')->nullable();
            $table->string('address')->nullable();
            $table->string('hometown')->nullable();
            $table->string('current_job')->nullable();
            $table->string('hobbies')->nullable();
            $table->string('drink_volume')->nullable();
            $table->boolean('smoke')->default(false);
            $table->string('siblings')->nullable();
            $table->string('living_with')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('rank')->nullable();
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
