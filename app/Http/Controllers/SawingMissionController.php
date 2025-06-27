<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSawingMissionRequest;
use App\Models\PriceSettings;
use App\Models\SawingMission;
use App\Services\SawingMissionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

        $price_settings = PriceSettings::query()->latest()->first();
        $validated['yellow_sawing_price'] = $price_settings->yellow_sawing_price;
        $validated['white_sawing_price'] = $price_settings->white_sawing_price;

        SawingMission::query()->create($validated);
        return $this->sendResponse("done", 200);
    }

    public function show($id): JsonResponse
    {
        $sawing_mission = SawingMission::query()
            ->where('id', $id)
            ->with(['payPeriod', 'responsible'])
            ->first();
        if (!$sawing_mission || !$sawing_mission->payPeriod) {
            return $this->sendError("not_found");
        }

        $total_per_type = $sawing_mission->sawingRotations()
            ->where('type', '!=', 'initial')
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $total_per_person_per_type = $sawing_mission->sawingRotations()
            ->where('type', '!=', 'initial')
            ->join('sawing_rotation_person', 'sawing_rotations.id', '=', 'sawing_rotation_person.sawing_rotation_id')
            ->join('people', 'people.id', '=', 'sawing_rotation_person.person_id')
            ->select(
                'people.id',
                'people.name',
                'people.tr_name',
                'sawing_rotations.type',
                DB::raw('SUM(sawing_rotation_person.amount) as total')
            )
            ->groupBy('people.id', 'people.name', 'people.tr_name', 'sawing_rotations.type')
            ->get();

        $total_per_person = [];
        foreach ($total_per_person_per_type as $row) {
            if (!isset($total_per_person[$row->id])) {
                $total_per_person[$row->id] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'tr_name' => $row->tr_name,
                ];
            }
            $total_per_person[$row->id][$row->type] = $row->total;
        }

        $total_per_person = array_values($total_per_person);

        $total_per_stations = $sawing_mission->sawingRotations()
            ->where('type', '!=', 'initial')
            ->select(
                'sawing_station_id',
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('sawing_station_id', 'type')
            ->get();

        $rotations = $sawing_mission->sawingRotations()
            ->where('type', '!=', 'initial')
            ->select('sawing_station_id', 'type', 'amount')
            ->get()
            ->groupBy(['sawing_station_id', 'type']);

        return $this->sendResponse([
            'sawing_mission' => $sawing_mission,
            'rotations' => $rotations,
            'total_per_type' => $total_per_type,
            'total_per_person' => $total_per_person,
            'total_per_stations' => $total_per_stations
        ]);
    }

    public function update_status($id): JsonResponse
    {
        $validated = request()->validate([
            'status' => 'required|in:new,ready,finished',
        ]);
        $sawing_mission = SawingMission::query()
            ->where('id', $id)
            ->first();
        if (!$sawing_mission || !$sawing_mission->payPeriod) {
            return $this->sendError("not_found");
        }

        $sawing_mission->update([
            'status' => $validated['status'],
        ]);

        return $this->sendResponse("done");
    }
}
