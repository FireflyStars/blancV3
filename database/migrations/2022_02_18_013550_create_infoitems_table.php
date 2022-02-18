<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfoitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infoitems', function (Blueprint $table) {
            $table->id();
            $table->integer('ConveyorInExportStore', 2)->default(0);
            $table->string('CCStatus', 80)->default('')->nullable();
            $table->string('Size', 10)->default('')->nullable();
            $table->integer('ConveyorInExport', 1)->default(0);
            $table->string('SubOrderID', 80)->default('');
            $table->string('Status', 30)->default('In Process');
            $table->string('InvoiceID', 80)->default('');
            $table->string('ItemID', 80)->default('');
            $table->integer('PERC', 3)->default(50);
            $table->integer('NoBag', 11)->default(0);
            $table->integer('idPartner', 2)->default(0);
            $table->string('ConvoyerINOUT', 1)->nullable();
            $table->integer('PartnerINOUT', 1)->default(0);
            $table->integer('Actif', 1)->default(1);
            $table->integer('Vip', 1)->default(0);
            $table->integer('id_items', 1)->default(0);
            $table->string('Label', 40)->nullable();
            $table->string('Patterns', 40)->nullable();
            $table->string('Fabrics', 40)->nullable();
            $table->text('Colors')->nullable();
            $table->integer('TimingChange', 1)->default(0);
            $table->integer('PriceChange', 1)->default(0);
            $table->integer('postSpot', 3)->default(0);
            $table->string('DepartmentName', 30)->nullable();
            $table->date('PromisedDate')->nullable();
            $table->text('process')->default('');
            $table->integer('nextpost', 3)->default(14);
            $table->string('dateJour', 40)->nullable();
            $table->string('StoreName', 40)->nullable();
            $table->string('store', 40)->nullable();
            $table->integer('express', 1)->default(0);
            $table->string('aspect', 40)->nullable();
            $table->string('garmentCare', 40)->nullable();
            $table->string('brandLabel', 40)->nullable();
            $table->string('generalState', 40)->nullable();
            $table->string('Complexities', 40)->nullable();
            $table->integer('star', 1)->default(0);
            $table->string('brand', 40)->nullable();
            $table->text('damage')->nullable();
            $table->string('typeitem', 40)->nullable();
            $table->float('priceTotal', 10, 2)->default(0);
            $table->float('priceClean', 10, 2)->default(0);
            $table->float('priceTail', 10, 2)->default(0);
            $table->string('ItemTrackingKey', 11)->nullable();
            $table->integer('id_invoice', 11)->default(0);
            $table->dateTime('date_add');
            $table->string('conveyorType', 3)->default('');
            $table->string('conveyorSlot', 5)->default('');
            $table->string('ConvStoreCode', 2)->default('');
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
        Schema::dropIfExists('infoitems');
    }
}
