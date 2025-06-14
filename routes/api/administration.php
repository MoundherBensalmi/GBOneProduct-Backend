<?php

use App\Http\Controllers\PayPeriodController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'administration',
], function () {

    // -------------------people-------------------
    Route::group([
        'prefix' => 'people',
        'middleware' => ['gb-auth'],
    ], function () {
        Route::get('', [PeopleController::class, 'index']);
        Route::post('check', [PeopleController::class, 'check']);
        Route::post('store', [PeopleController::class, 'store']);
        Route::post('recover/{id}', [PeopleController::class, 'recover']);
    });

    // -------------------positions-------------------
    Route::group([
        'prefix' => 'positions',
        'middleware' => ['gb-auth'],
    ], function () {
        Route::get('', [PositionController::class, 'index']);
    });

    // -------------------pay periods-------------------
    Route::group([
        'prefix' => 'pay-periods',
        'middleware' => ['gb-auth'],
    ], function () {
        Route::get('', [PayPeriodController::class, 'index']);
        Route::get('show/{id}', [PayPeriodController::class, 'show']);
    });
});
