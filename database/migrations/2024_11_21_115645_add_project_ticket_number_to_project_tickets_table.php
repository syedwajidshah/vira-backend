<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('project_tickets', function (Blueprint $table) {
            $table->integer('project_ticket_number')->nullable()->after('module_id');
        });
    }
    

    
};
