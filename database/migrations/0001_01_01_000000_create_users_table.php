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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic user information
            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('phone_number', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('verified')->default(false);
            $table->rememberToken();
            $table->timestamps();

            // Student-specific fields (nullable - only for parent_student role)
            $table->string('matricule')->nullable()->unique();
            $table->string('classroom')->nullable();
            $table->string('academic_year')->nullable();

            // Parent 1 information
            $table->string('parent1_name')->nullable();
            $table->string('parent1_surname')->nullable();
            $table->string('parent1_phone', 20)->nullable();

            // Parent 2 information
            $table->string('parent2_name')->nullable();
            $table->string('parent2_surname')->nullable();
            $table->string('parent2_phone', 20)->nullable();

            // Indexes for frequently queried fields
            $table->index('matricule');
            $table->index('classroom');
            $table->index('academic_year');
            $table->index('verified');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
