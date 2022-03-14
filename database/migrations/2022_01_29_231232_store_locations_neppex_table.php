<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StoreLocationsNeppexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_locations_neppex', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('neppex_control_id');
            $table->foreign('neppex_control_id')->references('id')->on('neppex_controls');
            $table->unsignedBigInteger('storage_location_id');
            $table->foreign('storage_location_id')->references('id')->on('storage_locations');
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
        Schema::dropIfExists('store_locations_neppex');
    }
}
