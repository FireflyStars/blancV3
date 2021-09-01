<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuggestedDeliveryDateToInfoOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('infoOrder', function (Blueprint $table) {
            //
            $table->addColumn('DATE','suggestedDeliveryDate')->after('PickupID')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('infoOrder', function (Blueprint $table) {
            //
            $table->dropColumn('suggestedDeliveryDate');
        });
    }
}
