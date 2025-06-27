<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sawing_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pay_period_id')->constrained('pay_periods');
            $table->foreignId('assigned_person_id')->constrained('people');

            $table->date('date');
            $table->string('start_time');
            $table->string('end_time');

            $table->enum('status', ['new', 'ready', 'finished'])->default('new');

            $table->decimal('yellow_sawing_price');
            $table->decimal('white_sawing_price');

            $table->timestamps();
        });

        Schema::create('sorting_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pay_period_id')->constrained('pay_periods');
            $table->foreignId('assigned_person_id')->constrained('people');

            $table->date('date');
            $table->string('start_time');
            $table->string('end_time');

            $table->enum('status', ['new', 'ready', 'finished'])->default('new');

            $table->decimal('white_sorting_price');
            $table->decimal('yellow_sorting_price');
            $table->decimal('trimming_price');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sawing_missions');
        Schema::dropIfExists('sorting_missions');
    }
};
