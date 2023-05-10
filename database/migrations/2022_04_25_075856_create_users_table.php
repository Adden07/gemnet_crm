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
            $table->id();
            $table->integer('admin_id');
            $table->integer('city_id');
            $table->integer('area_id');
            $table->integer('subarea_id');
            $table->string('name',191);
            $table->string('username',20);
            $table->string('password',191);
            $table->string('portal_pass',191)->nullable();
            $table->string('nic');
            $table->string('mobile');
            $table->enum('user_status',['active','disble','registered','expired']);
            $table->text('address');
            $table->enum('status',['disabled', 'active']);
            $table->bigInteger('qt_total')->nullable();
            $table->bigInteger('qt_used')->nullable();
            $table->text('nic_front')->nullable();
            $table->text('nic_back')->nullable();
            $table->text('user_form_front')->nullable();
            $table->text('user_form_back')->nullable();
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
