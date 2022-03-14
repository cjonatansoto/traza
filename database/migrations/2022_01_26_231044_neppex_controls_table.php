<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NeppexControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('neppex_controls', function (Blueprint $table) {
            $table->id();
            $table->string('codaut')->unique();
            $table->string('transfer_code')->unique();
            $table->dateTime('authorization_date');
            $table->string('container');
            $table->string('container_name');
            $table->tinyInteger('issue_certificate');
            $table->unsignedBigInteger('shipping_port_id');
            $table->foreign('shipping_port_id')->references('id')->on('shipping_ports');
            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedBigInteger('destination_port_id');
            $table->foreign('destination_port_id')->references('id')->on('destination_ports');
            $table->unsignedBigInteger('exporter_id');
            $table->foreign('exporter_id')->references('id')->on('exporters');
            $table->unsignedBigInteger('border_crossing_id');
            $table->foreign('border_crossing_id')->references('id')->on('border_crossings');
            $table->unsignedBigInteger('consignee_id');
            $table->foreign('consignee_id')->references('id')->on('consignees');
            $table->bigInteger('transport_id')->unsigned();
            $table->foreign('transport_id')->references('id')->on('transports');
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->bigInteger('updated_by')->unsigned();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->tinyInteger('inactive')->default(0);
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
        Schema::dropIfExists('neppex_controls');
    }
}
