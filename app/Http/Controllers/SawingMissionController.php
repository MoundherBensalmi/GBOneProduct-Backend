<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitSawingStationRequest;
use App\Http\Requests\StoreSawingRotationRequest;
use App\Http\Requests\UpdateSawingRotationRequest;
use App\Models\PayPeriod;
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

    public function show($mission_id): JsonResponse
    {
        $sawing_mission_data = $this->sawingMissionService->is_active($mission_id);
        if (!$sawing_mission_data['is_active']) {
            return response()->json([
                'message' => 'Sawing mission not found',
            ], 404);
        }

        $sawing_stations = SawingStation::query()
            ->with([
                'rotations' => function ($query) use ($mission_id) {
                    $query->where('sawing_mission_id', $mission_id);
                },
            ])
            ->get();

        return response()->json([
            'sawing_mission' => $sawing_mission_data['sawing_mission'],
            'sawing_stations' => $sawing_stations,
        ]);
    }

    public function init_station(InitSawingStationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $sawing_mission_data = $this->sawingMissionService->is_active($validated['mission_id']);
        if (!$sawing_mission_data['is_active']) {
            return response()->json([
                'message' => 'Sawing mission not found',
            ], 404);
        }

        $initial_rotation = $this->sawingMissionService->init_station(
            $validated['station_id'],
            $validated['mission_id'],
            $validated['people']
        );

        return response()->json([
            'message' => 'Sawing station initialized successfully',
            'initial_rotation' => $initial_rotation,
        ]);
    }

    public function init(Request $request): JsonResponse
    {
        $mission_id = $request->integer('mission_id');
        ['is_active' => $is_active, 'sawing_mission' => $sawing_mission] = $this->sawingMissionService->is_active($mission_id);
        if (!$is_active) {
            return response()->json([
                'message' => 'Sawing mission not found',
            ], 404);
        }

        $sawing_mission->update([
            'is_started' => true,
        ]);

        return response()->json([
            'message' => 'Sawing mission started successfully',
            'sawing_mission' => $sawing_mission,
        ]);
    }

    public function close(Request $request): JsonResponse
    {
        $mission_id = $request->integer('mission_id');
        ['is_active' => $is_active, 'sawing_mission' => $sawing_mission] = $this->sawingMissionService->is_active($mission_id);
        if (!$is_active) {
            return response()->json([
                'message' => 'Sawing mission not found',
            ], 404);
        }

        $sawing_mission->update([
            'is_started' => true,
            'is_finished' => true,
        ]);

        return response()->json([
            'message' => 'Sawing mission closed successfully',
            'sawing_mission' => $sawing_mission,
        ]);
    }

    public function store(StoreSawingRotationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        ['is_active' => $is_active] = $this->sawingMissionService->is_active($validated['mission_id']);
        if (!$is_active) {
            return response()->json([
                'message' => 'Sawing mission not found',
            ], 404);
        }

        $rotation = $this->sawingMissionService->create_rotation(
            $validated['station_id'],
            $validated['mission_id'],
            $validated['people'],
            $validated['amount']
        );

        return response()->json([
            'message' => 'Sawing rotation created successfully',
            'rotation' => $rotation,
        ]);
    }

    public function update(UpdateSawingRotationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        ['is_active' => $is_active] = $this->sawingMissionService->is_active($validated['mission_id']);
        if (!$is_active) {
            return response()->json([
                'message' => 'Sawing mission not found',
            ], 404);
        }

        $rotation = $this->sawingMissionService->update_rotation(
            $validated['rotation_id'],
            $validated['people'],
            $validated['amount']
        );

        return response()->json([
            'message' => 'Sawing rotation updated successfully',
            'rotation' => $rotation,
        ]);
    }
}
