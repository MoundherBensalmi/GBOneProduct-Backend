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
        Schema::create('sawing_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sawing_mission_id')
                ->constrained('sawing_missions');

            $table->boolean('is_initial')->default(false);
            $table->decimal('amount')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sawing_rotation_person', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sawing_rotation_id')
                ->constrained('sawing_rotations');
            $table->foreignId('person_id')
                ->constrained('people') ;

            $table->decimal('amount')->default(0);
            $table->timestamps();

            $table->unique(['sawing_rotation_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sawing_rotation_person');
        Schema::dropIfExists('sawing_rotations');
    }
};
