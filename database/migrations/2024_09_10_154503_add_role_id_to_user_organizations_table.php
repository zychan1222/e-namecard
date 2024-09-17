<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('user_organization', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->after('organization_id');
    
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('user_organization', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
    
};
