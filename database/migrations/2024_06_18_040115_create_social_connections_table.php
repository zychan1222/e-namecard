<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialConnectionsTable extends Migration
{
    public function up()
    {
        Schema::create('social_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('provider');
            $table->string('provider_id');
            $table->string('access_token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_connections');
    }
}
