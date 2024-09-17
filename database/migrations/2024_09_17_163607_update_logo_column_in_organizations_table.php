<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLogoColumnInOrganizationsTable extends Migration
{
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('logo')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('logo')->nullable(false)->change();
        });
    }
}
