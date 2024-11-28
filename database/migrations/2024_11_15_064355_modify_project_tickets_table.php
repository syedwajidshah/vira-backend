<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyProjectTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('project_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id'); // Add module_id column
            $table->dropColumn('sub_project_id'); 
        });
    }

   
}
