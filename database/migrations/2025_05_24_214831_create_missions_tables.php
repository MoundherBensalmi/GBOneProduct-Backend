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
        Schema::create('sawing_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pay_period_id')
                ->constrained('pay_periods');

            $table->date('date');
            $table->string('start_time');
            $table->string('end_time');

            $table->boolean('is_started')->default(false);
            $table->boolean('is_finished')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sawing_missions');
    }
};
