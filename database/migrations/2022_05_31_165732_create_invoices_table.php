<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_id');
            $table->integer('admin_id');
            $table->integer('user_id');
            $table->integer('pkg_id');
            $table->float('cost_1',8,2);
            $table->float('total_cost',8,2);
            $table->float('franchise_cost',8,2)->default(0);
            $table->float('dealer_cost',8,2)->default(0);
            $table->float('subdealer_cost',8,2)->default(0);
            $table->enum('type',['new','renew']);
            $table->dateTime('current_exp_date')->nullable();
            $table->dateTime('new_exp_date');
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
        Schema::dropIfExists('invoices');
    }
}
