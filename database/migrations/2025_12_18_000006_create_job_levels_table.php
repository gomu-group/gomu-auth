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

        Schema::create('account.job_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level_name', 50);
            $table->integer('level_rank')->unique();
            $table->softDeletes();

            $table->index('level_rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account.job_levels');
    }
};