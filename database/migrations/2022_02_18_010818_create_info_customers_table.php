<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infoCustomer', function (Blueprint $table) {
            $table->id();
            $table->tinyInt('Actif')->default(1);
            $table->float('discount', 10, 2)->default(0);
            $table->tinyInt('bycard')->default(1);
            $table->float('credit', 10, 2)->default(0);
            $table->text('CustomerNotes')->default('')->nullable();
            $table->string('RevenueLocation', 30)->default('DELIVERY')->nullable();
            $table->string('InvoiceEmail', 80)->default('')->nullable();
            $table->string('ErrorSpot', 80)->default('')->nullable();
            $table->integer('IsConnectToSpot', 1)->default(0)->nullable();
            $table->string('MainService', 80)->default('')->nullable();
            $table->float('AvOrder', 10, 2)->default(0)->nullable();
            $table->float('FreqBooking', 10, 2)->default(0)->nullable();
            $table->float('FreqVisit', 10, 2)->default(0)->nullable();
            $table->string('ReferralSource', 80)->default('')->nullable();
            $table->integer('IsMasterAccount', 1)->default(0)->nullable();
            $table->string('CustomerIDMasterAccount', 80)->default('');
            $table->integer('OrderRecurring', 11)->default(0);
            $table->string('CustomerID', 80)->default('');
            $table->date('SignupDateOnline')->default('2000-01-01');
            $table->date('SignupDate')->default('2000-01-01');
            $table->string('CompanyName', 80)->default('')->nullable();
            $table->integer('AcceptPrivacy', 1)->default(0);
            $table->integer('AcceptTerms', 1)->default(0);
            $table->integer('AcceptMarketing', 1)->default(0);
            $table->integer('AccountInApp', 1)->default(0);
            $table->integer('TransactioCom', 1)->default(1);
            $table->integer('MarketingValidation', 1)->default(0);
            $table->date('PauseDateTo')->nullable();
            $table->date('PauseDateFrom')->nullable();
            $table->string('CustomerIDMaster', 80)->default('');
            $table->integer('IsMaster', 1)->default(0);
            $table->string('TypeDelivery', 30)->default('DELIVERY');
            $table->string('PassWordAPI', 80)->default('');
            $table->text('DeliveryMon')->default('');
            $table->text('DeliveryTu')->default('');
            $table->text('DeliveryWed')->default('');
            $table->text('DeliveryTh')->default('');
            $table->text('DeliveryFri')->default('');
            $table->text('DeliverySat')->default('');
            $table->integer('DeliverybyDay', 2)->default(0);
            $table->time('tranchetoDelivery')->nullable();
            $table->time('tranchefromDelivery')->nullable();
            $table->text('commentDelivery')->nullable();
            $table->text('Phone')->nullable();
            $table->string('Name', 80)->nullable();
            $table->string('EmailAddress', 80);
            $table->string('LastName', 50);
            $table->string('FirstName', 50)->default('');
            $table->integer('id_customer', 11)->default(0);
            $table->string('Title', 10)->nullable();
            $table->integer('id_address_invoice', 11)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infoCustomer');
    }
}
