<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationIdToModelHasRoles extends Migration
{
    public function up()
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade'); 
            // Add foreign key to `organization_id` that references the organizations table
        });
    }

    public function down()
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
}
