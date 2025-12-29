<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('department_id')->nullable();
            $table->string('nip')->unique()->nullable();
            $table->string('nik', 25)->unique()->nullable();
            $table->string('full_name', 150);
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('personal_email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->date('join_date');
            $table->date('termination_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('nik');
            $table->index('full_name');
            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};