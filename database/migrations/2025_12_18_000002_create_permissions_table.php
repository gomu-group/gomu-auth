<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'account' schema if it doesn't exist
        DB::statement('CREATE SCHEMA IF NOT EXISTS account');

        Schema::create('account.permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('permission_key', 100)->unique();
            $table->string('module_name', 50);
            $table->softDeletes();

            $table->index('module_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account.permissions');
    }
};