<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SlaughterPlacesNeppexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slaughter_places_neppex', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('neppex_control_id');
            $table->foreign('neppex_control_id')->references('id')->on('neppex_controls');
            $table->unsignedBigInteger('slaughter_place_id');
            $table->foreign('slaughter_place_id')->references('id')->on('slaughter_places');
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
        Schema::dropIfExists('slaughter_places_neppex');
    }
}
