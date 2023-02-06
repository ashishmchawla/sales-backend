<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadAmounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_amounts', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id')->unsigned();
            $table->integer('month');
            $table->integer('year');
            $table->integer('marginValue')->default('0');
            $table->integer('mfValue')->default('0');
            $table->integer('insuranceValue')->default('0');
            $table->integer('optValue')->default('0');
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
        Schema::dropIfExists('lead_amounts');
    }
}
