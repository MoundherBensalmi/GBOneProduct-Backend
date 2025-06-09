<?php

namespace App\Http\Controllers;

use App\Models\SawingStation;
use Illuminate\Http\JsonResponse;

class SawingStationController extends Controller
{
    public function index(): JsonResponse
    {
        $sawing_stations = SawingStation::query()->get();
        return $this->sendResponse([
            'sawing_stations' => $sawing_stations,
        ]);
    }
}
