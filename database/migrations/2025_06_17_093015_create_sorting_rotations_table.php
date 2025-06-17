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
        Schema::create('sorting_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorting_mission_id')->constrained('sorting_missions');
            $table->foreignId('person_id')->constrained('people');

            $table->enum('type', ['initial', 'yellow_sorting', 'white_sorting', 'trimming']);
            $table->decimal('amount', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorting_rotations');
    }
};
