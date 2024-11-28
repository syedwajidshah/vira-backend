<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('project_id'); // Primary key
            $table->string('project_name');
            $table->date('project_start_date');
            $table->date('project_end_date');
            $table->integer('project_status')->nullable(); // Status as integer
            $table->unsignedBigInteger('project_manager')->nullable(); // No foreign key constraint
            $table->timestamps(); // Adds created_at and updated_at columns
            $table->softDeletes(); // Adds deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
