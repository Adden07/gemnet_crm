<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id');
            $table->integer('city_id');
            $table->text('logo')->nullable();
            $table->text('favicon')->nullable();
            $table->string('company_name',191);
            $table->string('email',191);
            $table->string('slogan',191)->nullable();
            $table->string('mobile',15);
            $table->text('address');
            $table->string('country',191);
            $table->string('zipcode',191);
            $table->string('copyright',191);
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
        Schema::dropIfExists('settings');
    }
}
