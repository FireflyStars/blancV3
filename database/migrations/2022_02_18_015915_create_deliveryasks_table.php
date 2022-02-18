<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveryask', function (Blueprint $table) {
            $table->id();
            $table->string('Error', 80)->default('');
            $table->string('PhoneNumber', 30)->default('')->nullable();
            $table->string('CodeCountry', 5)->default('')->nullable();
            $table->string('TypeDelivery', 80)->default('')->nullable();
            $table->string('AddressID', 80)->default('');
            $table->string('CustomerID', 80)->default('');
            $table->string('DeliveryaskID', 80)->default('');
            $table->integer('id_customer', 11)->nullable();
            $table->text('comment')->nullable();
            $table->time('trancheFrom');
            $table->time('trancheto');
            $table->integer('address_id')->default(0);
            $table->string('status', 15)->default('NEW');
            $table->date('date');
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
        Schema::dropIfExists('deliveryask');
    }
}
