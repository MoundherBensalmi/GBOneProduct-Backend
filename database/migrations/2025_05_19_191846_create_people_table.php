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
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('tr_name')->nullable();
            $table->string('phone')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('people_positions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('payment_type', ['fixed_salary', 'variable_salary', 'objective']);
            $table->integer('salary')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
