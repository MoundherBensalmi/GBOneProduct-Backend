<?php

namespace App\Http\Controllers;

use App\Models\SawingStation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SawingStationController extends Controller
{
    public function index(): JsonResponse
    {
        $sawing_stations = SawingStation::query()->get();
        return $this->sendResponse([
            'sawing_stations' => $sawing_stations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        SawingStation::query()->create($validated);
        return $this->sendResponse("done");
    }
}
