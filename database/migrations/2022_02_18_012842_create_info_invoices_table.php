<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infoInvoice', function (Blueprint $table) {
            $table->id();
            $table->tinyInt('soldspot')->default(0);
            $table->string('CCStatus', 80)->default('');
            $table->dateTime('datesold')->nullable();
            $table->integer('Paid', 1)->default(0);
            $table->string('SubOrderID', 80)->default('');
            $table->string('OrderID', 80)->default('');
            $table->string('Status', 30)->default('In Process');
            $table->string('AddressID', 80)->default('');
            $table->string('CustomerID', 80)->default('');
            $table->string('InvoiceID', 80)->default('');
            $table->string('Departement', 40)->default('N/A');
            $table->string('StoreName', 20)->nullable();
            $table->integer('PieceCount', 5)->default(1);
            $table->integer('id_address', 11)->default(0);
            $table->integer('id_customer', 11)->default(0);
            $table->string('Client', 80);
            $table->string('NumInvoice', 20);
            $table->integer('id_invoice', 11);
            $table->dateTime('date_add');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infoInvoice');
    }
}
