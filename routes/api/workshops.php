<?php

use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => ['gb-auth'],
], function () {
    Route::get('people/production', [WorkshopController::class, 'production_people']);
    Route::get('sawing-stations', [WorkshopController::class, 'sawing_stations']);
    Route::get('work-missions/mine', [WorkshopController::class, 'my_missions']);

    Route::post('work-missions/sawing/close', [WorkshopController::class, 'close_sawing_mission']);

});



// Sawing Missions
//Route::get('work-missions/sawing/show/{mission_id}', [SawingMissionController::class, 'show']);
//Route::post('work-missions/sawing/store', [SawingMissionController::class, 'store']);
//Route::post('work-missions/sawing/station/init', [SawingMissionController::class, 'init_station']);
//Route::post('work-missions/sawing/init', [SawingMissionController::class, 'init']);
//Route::post('work-missions/sawing/close', [SawingMissionController::class, 'close']);
//Route::post('work-missions/sawing/rotations/store', [SawingMissionController::class, 'store']);
//Route::post('work-missions/sawing/rotations/update', [SawingMissionController::class, 'update']);
