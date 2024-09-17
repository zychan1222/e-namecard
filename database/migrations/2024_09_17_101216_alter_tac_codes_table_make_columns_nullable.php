<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTacCodesTableMakeColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tac_codes', function (Blueprint $table) {
            // Make tac_code and expires_at nullable
            $table->string('tac_code')->nullable()->change();
            $table->timestamp('expires_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tac_codes', function (Blueprint $table) {
            // Revert tac_code and expires_at back to NOT NULL if needed
            $table->string('tac_code')->nullable(false)->change();
            $table->timestamp('expires_at')->nullable(false)->change();
        });
    }
}
