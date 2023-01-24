<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id');
            $table->integer('month');
            $table->integer('year');
            $table->enum('target_type', ['new', 'existing', 'account', 'margin', 'mutual_funds', 'insurance', 'third_party']);
            $table->integer('count');
            $table->integer('targets');
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
        Schema::dropIfExists('user_targets');
    }
}
