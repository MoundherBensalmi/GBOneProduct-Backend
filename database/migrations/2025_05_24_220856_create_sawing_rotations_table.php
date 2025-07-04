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
        Schema::create('sawing_rotations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sawing_mission_id')->constrained('sawing_missions');
            $table->foreignId('sawing_station_id')->constrained('sawing_stations');

            $table->enum('type', ['initial', 'yellow_sawing', 'white_sawing']);
            $table->decimal('amount', 10, 2)->default(0);

            $table->timestamps();
        });

        Schema::create('sawing_rotation_person', function (Blueprint $table) {
            $table->foreignId('sawing_rotation_id')->constrained('sawing_rotations');

            $table->foreignId('person_id')->constrained('people');

            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();

            $table->primary(['sawing_rotation_id', 'person_id']);
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
