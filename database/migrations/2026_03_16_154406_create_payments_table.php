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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign keys
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('fee_id')->nullable()->constrained('fees')->onDelete('set null');

            // Transaction attributes
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('status'); // pending, completed, failed, refunded
            $table->string('kkiapay_reference')->nullable();
            $table->string('phone_number', 20);

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('date');
            $table->index('kkiapay_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
