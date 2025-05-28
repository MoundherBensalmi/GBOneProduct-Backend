<?php

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SawingMissionController;
use App\Http\Controllers\WorkSessionController;
use Illuminate\Support\Facades\Route;

Route::get('work-sessions', [WorkSessionController::class, 'index']);
Route::get('work-sessions/{session}', [WorkSessionController::class, 'show']);
Route::post('work-sessions/store', [WorkSessionController::class, 'store']);

// Sawing Missions
Route::get('work-missions/sawing/show/{mission_id}', [SawingMissionController::class, 'show']);
Route::post('work-missions/sawing/store', [SawingMissionController::class, 'store']);
Route::post('work-missions/sawing/station/init', [SawingMissionController::class, 'init_station']);
Route::post('work-missions/sawing/init', [SawingMissionController::class, 'init']);
Route::post('work-missions/sawing/close', [SawingMissionController::class, 'close']);
Route::post('work-missions/sawing/rotations/store', [SawingMissionController::class, 'store']);
Route::post('work-missions/sawing/rotations/update', [SawingMissionController::class, 'update']);


// people
Route::get('/people', [PeopleController::class, 'index']);
