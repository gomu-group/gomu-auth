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

        Schema::create('account.users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username', 50)->nullable()->unique();
            $table->string('email', 100)->unique();
            $table->text('password_hash');
            $table->boolean('force_password_change')->default(true);
            $table->enum('user_type', ['internal', 'external'])->default('internal');
            $table->uuid('role_id')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('username');
            $table->index('email');
            $table->index('user_type');
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        Schema::create('account.password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('account.sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')->references('id')->on('account.users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account.sessions');
        Schema::dropIfExists('account.password_reset_tokens');
        Schema::dropIfExists('account.users');
        DB::statement('DROP SCHEMA IF EXISTS account CASCADE');
    }
};