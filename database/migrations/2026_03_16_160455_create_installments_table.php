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
        Schema::create('installments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign key to tuition fee
            $table->foreignUuid('tuition_fee_id')->constrained('fees')->onDelete('cascade');

            // Installment attributes
            $table->integer('number'); // installment number (1, 2, 3, etc.)
            $table->decimal('amount', 10, 2);
            $table->date('due_date');

            $table->timestamps();

            // Indexes
            $table->index('tuition_fee_id');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
