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
        Schema::create('job_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level_name', 50);
            $table->integer('level_rank')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->index('level_rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_levels');
    }
};