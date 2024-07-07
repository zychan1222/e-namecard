<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_cn')->nullable();
            $table->string('designation');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_pic')->nullable();
            $table->string('department');
            $table->string('company_name');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}

