<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('project_tickets', function (Blueprint $table) {
            $table->id('ticket_id'); // Primary key for tickets
            $table->string('ticket_title');
            $table->date('ticket_start_date');
            $table->date('ticket_end_date');
            $table->string('ticket_status');
            $table->unsignedBigInteger('sub_project_id'); // Reference to sub project, no foreign key constraint
            $table->timestamps();
            $table->softDeletes(); // Allows soft deletion
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_tickets');
    }
}
