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
        Schema::create('fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->string('type'); // RegistrationFee, TuitionFee, GeneralFee

            // Base Fee attributes (common to all fee types)
            $table->decimal('total_amount', 10, 2);
            $table->string('academic_year');
            $table->string('title');
            $table->string('classroom')->nullable();
            $table->text('description')->nullable();

            // TuitionFee specific attribute
            $table->integer('number_of_installments')->nullable();

            // GeneralFee specific attribute
            $table->boolean('required')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('academic_year');
            $table->index('classroom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
