<?php

use App\Http\Controllers\SawingMissionController;
use App\Http\Controllers\WorkSessionController;
use Illuminate\Support\Facades\Route;

Route::get('work-sessions', [WorkSessionController::class, 'index']);
Route::get('work-sessions/{session}', [WorkSessionController::class, 'show']);
Route::post('work-sessions/store', [WorkSessionController::class, 'store']);

// Sawing Missions
Route::get('work-missions/sawing/show/{mission}', [SawingMissionController::class, 'show']);
Route::post('work-missions/sawing/store', [SawingMissionController::class, 'store']);
Route::post('work-missions/sawing/{mission}/station/{station}/init', [SawingMissionController::class, 'init-station']);
Route::post('work-missions/sawing/{mission}/init', [SawingMissionController::class, 'init']);
Route::post('work-missions/sawing/{mission}/close', [SawingMissionController::class, 'close']);
Route::post('work-missions/sawing/{mission}/rotations', [SawingMissionController::class, 'store']);
