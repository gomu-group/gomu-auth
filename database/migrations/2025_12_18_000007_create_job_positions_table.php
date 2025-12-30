<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS account');
        Schema::create('account.job_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100);
            $table->uuid('department_id');
            $table->uuid('job_level_id');
            $table->softDeletes();

            $table->foreign('department_id')->references('id')->on('account.departments')->onDelete('restrict');
            $table->foreign('job_level_id')->references('id')->on('account.job_levels')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account.job_positions');
    }
};