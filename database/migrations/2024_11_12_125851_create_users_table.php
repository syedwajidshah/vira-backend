<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_name');
            $table->string('user_email')->unique();
            $table->string('user_designation')->nullable();
            $table->string('user_role')->nullable();
            $table->timestamps();
            $table->softDeletes();  // For soft deletes (deleted_at)
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
