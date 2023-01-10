<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadActivitiesTable extends Migration
{

    public function up()
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('lead_id');
            $table->text('activity_log');
            $table->enum('activity_type', ['note', 'reminder']);
            $table->dateTime('remind_at');
            $table->boolean('is_event_complete');
            $table->foreignUuid('logged_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_activities');
    }
    
}
