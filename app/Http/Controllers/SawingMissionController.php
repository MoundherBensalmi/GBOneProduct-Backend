<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitSawingStationRequest;
use App\Http\Requests\StoreSawingMissionRequest;
use App\Http\Requests\StoreSawingRotationRequest;
use App\Http\Requests\UpdateSawingRotationRequest;
use App\Models\PayPeriod;
use App\Models\SawingMission;
use App\Models\SawingStation;
use App\Services\SawingMissionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SawingMissionController extends Controller
{
    protected SawingMissionServices $sawingMissionService;

    public function __construct(SawingMissionServices $sawingMissionServices)
    {
        $this->sawingMissionService = $sawingMissionServices;
    }

    public function store(StoreSawingMissionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        SawingMission::query()->create($validated);
        return $this->sendResponse("done", 200);
    }

}
