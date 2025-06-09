<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('tr_name')->nullable();
            $table->boolean('deletable')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('positions')->insert([
            [
                'id' => 1,
                'name' => 'عامل إداري',
                'tr_name' => 'Travailleur D\'Administration',
                'deletable' => false,
            ],
            [
                'id' => 2,
                'name' => 'عامل إنتاج',
                'tr_name' => 'Travailleur De Production ',
                'deletable' => false,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
