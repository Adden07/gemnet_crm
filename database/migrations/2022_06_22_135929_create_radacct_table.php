<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRadacctTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radacct', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('radacctid ');
            $table->string('acctsessionid',64);
            $table->string('acctuniqueid',32);
            $table->string('username',64);
            $table->string('groupname',64);
            $table->string('realm',64);
            $table->string('nasipaddress',15);
            $table->string('nasportid',100);
            $table->string('nasporttype',32);
            $table->dateTime('acctstarttime');
            $table->dateTime('acctupdatetime');
            $table->dateTime('acctstoptime');
            $table->integer('acctinterval');
            $table->integer('acctsessiontime');
            $table->string('cctauthentic',32);
            $table->string('connectinfo_start',50);
            $table->string('connectinfo_stop',50);
            $table->bigInteger('acctinputoctets');
            $table->bigInteger('acctoutputoctets');
            $table->string('calledstationid',50);
            $table->string('callingstationid',50);
            $table->string('acctterminatecause');
            $table->string('servicetype');
            $table->string('framedprotocol');
            $table->string('framedipaddress ');
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
        Schema::dropIfExists('radacct');
    }
}
