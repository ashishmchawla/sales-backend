<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('contact')->length(10);
            $table->string('location')->nullable();
            $table->string('account_category')->nullable();
            $table->string('account_code')->nullable();
            $table->string('third_party')->nullable();
            $table->float('stock_margin', 10, 2)->default(0);
            $table->enum('lead_status', ['open', 'appointment', 'lost', 'won'])->default('open');
            $table->foreignUuid('lead_owner');
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
        Schema::dropIfExists('leads');
    }
}
