<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_tmp', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('task_id');
            $table->dateTime('task_datetime');
            $table->integer('admin_id');
            $table->integer('city_id');
            $table->integer('area_id');
            $table->integer('subarea_id');
            $table->string('name',50);
            $table->string('username',50);
            $table->string('password',30);
            $table->string('nic',30);
            $table->string('mobile',25);
            $table->string('address',1000);
            $table->integer('package');
            $table->dateTime('expiration');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_tmp');
    }
}
