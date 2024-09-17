<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // Auto incrementing primary key
            $table->string('email');  // Allow multiple entries with the same email
            $table->string('password')->nullable();  // Optional, in case of future password login
            $table->string('profile_pic')->nullable();
            $table->string('phone')->nullable();
            $table->string('name_cn')->nullable();  // Optional Chinese name field
            $table->string('name');
            $table->string('designation')->nullable();  // Job title
            $table->string('department')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();  // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
