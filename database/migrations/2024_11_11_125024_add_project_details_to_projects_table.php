<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_name')->nullable(); // Add project_name
            $table->date('start_date')->nullable(); // Add start_date
            $table->date('end_date')->nullable(); // Add end_date
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['project_name', 'start_date', 'end_date']);
        });
    }
};
