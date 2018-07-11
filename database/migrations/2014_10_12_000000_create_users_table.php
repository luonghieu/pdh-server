<?php

use App\Enums\UserType;
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
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('nickname')->nullable();
            $table->string('fullname')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->tinyInteger('height')->nullable();
            $table->tinyInteger('salary_id')->nullable();
            $table->tinyInteger('body_type_id')->nullable();
            $table->unsignedInteger('prefecture_id')->nullable();
            $table->unsignedInteger('hometown_id')->nullable();
            $table->unsignedInteger('job_id')->nullable();
            $table->tinyInteger('drink_volume_type')->nullable();
            $table->tinyInteger('smoking_type')->nullable();
            $table->string('siblings_type')->nullable();
            $table->string('cohabitant_type')->nullable();
            $table->text('intro')->nullable();
            $table->tinyInteger('type')->default(UserType::GUEST);
            $table->boolean('status')->default(true);
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
