<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('edit_by_id');
            $table->integer('added_to_id');
            $table->integer('city_id')->nullable();
            $table->integer('setting_id')->nullable();
            $table->string('name',191);
            $table->string('username',191)->unique();
            $table->string('email',191);
            $table->string('password',191);
            $table->string('nic',20);
            $table->string('mobile',20)->nullable();
            $table->text('nic_front')->nullable();
            $table->text('nic_back')->nullable();
            $table->text('agreement')->nullable();
            $table->text('image')->nullable();
            $table->text('address')->nullable();
            $table->enum('user_type',['superadmin','admin','franchise', 'dealer', 'sub_dealer']);
            $table->enum('is_active',['active','deactive','ban'])->default('active');
            // $table->enum('is_allow_sub_dealer',['yes','no'])->nullable();
            $table->integer('mac')->default(0);
            $table->double('credit_limit',12,2);
            $table->double('balance',12,2);
            $table->rememberToken();
            $table->text('user_permissions')->nullable();
            $table->timestamp('last_login');
            $table->timestamps();


            // $table->string('mobile_no')->nullable();
            // $table->text('image')->nullable();
            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            // $table->boolean('is_verify')->default('0');
            // $table->string('user_type');
            // $table->text('user_permissions')->nullable();
            // $table->rememberToken();
            // $table->timestamp('deleted_at')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
