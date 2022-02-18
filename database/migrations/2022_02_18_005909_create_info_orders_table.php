<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infoOrder', function (Blueprint $table) {
            $table->id();
            $table->string('deliverymethod')->default('');
            $table->integer('Paid')->default(0);
            $table->date('suggestedDeliveryDate')->nullable();
            $table->float('SumItemUpcharges')->default(0);
            $table->float('SumItemDiscounts')->default(0);
            $table->float('Subtotal')->default(0)->comment(' - ItemDiscount+ ItemCleanPrice + ItemTailPrice ');
            $table->float('Total')->default(0);
            $table->float('OrderUpcharge')->default(0);
            $table->integer('Split')->default(0);
            $table->integer('FailedDelivery')->default(0);
            $table->string('TypeDelivery', 30)->default('DELIVERY');
            $table->date('DateDeliveryAsk')->default('2020-01-01');
            $table->date('DatePickup')->default('2020-01-01');
            $table->string('PickupID', 80)->default('');
            $table->string('DeliveryaskID', 80)->default('');
            $table->integer('CanBeCancelled')->default(1)->comment('0 : Pas cancelled');
            $table->string('CustomerID', 80)->default('');
            $table->string('OrderID', 80)->default('')->comment('N order');
            $table->string('Status', 30)->default('In Process');
            $table->tinyInt('firstorder')->default(0);
            $table->tinyInt('fromapp')->default(0);
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
        Schema::dropIfExists('infoOrder');
    }
}
