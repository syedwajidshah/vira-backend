<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignTasksTable extends Migration
{
    public function up()
    {
        Schema::create('assign_tasks', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('assign_by'); // ID of the user creating the assignment
            $table->timestamps();
        });
    }

}
