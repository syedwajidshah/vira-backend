<?php
// database/migrations/xxxx_xx_xx_create_sub_projects_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('sub_projects', function (Blueprint $table) {
            $table->id('sub_project_id');
            $table->string('sub_project_name');
            $table->date('sub_project_start_date');
            $table->date('sub_project_end_date');
            $table->string('sub_project_status');
            $table->unsignedBigInteger('sub_project_manager'); // No foreign key constraint
            $table->unsignedBigInteger('project_id');          // No foreign key constraint
            $table->timestamps();
            $table->softDeletes();
        });
    }

   
}
