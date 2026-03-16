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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignUuid('installment_id')->nullable()->after('fee_id');
            $table->foreign('installment_id')
                  ->references('id')
                  ->on('installments')
                  ->onDelete('set null');
            $table->index('installment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['installment_id']);
            $table->dropIndex(['installment_id']);
            $table->dropColumn('installment_id');
        });
    }
};
