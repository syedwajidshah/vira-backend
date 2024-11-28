<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubProjectUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_project_users', function (Blueprint $table) {
            $table->increments('sub_project_user_id');
            $table->unsignedInteger('sub_project_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });
    }
   
}
