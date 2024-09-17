<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTacCodesTable extends Migration
{
    public function up()
    {
        Schema::create('tac_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email');  
            $table->string('tac_code');  
            $table->timestamp('expires_at');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('tac_codes');
    }
}
