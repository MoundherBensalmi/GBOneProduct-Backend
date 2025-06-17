<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSawingMissionRequest;
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
        SawingMission::query()->create($validated);
        return $this->sendResponse("done", 200);
    }

    public function show($id): JsonResponse
    {
        $sawing_mission = SawingMission::query()
            ->where('id', $id)
            ->with([
                'payPeriod',
                'assignedUser' => fn($q) => $q->withTrashed()->with([
                    'person' => fn($q) => $q->withTrashed()
                ])
            ])
            ->first();
        if (!$sawing_mission) {
            return $this->sendResponse("not_found", "not_found");
        }

        $rotations = $sawing_mission->sawingRotations()
            ->where('type', '!=', 'initial')
            ->get();

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

        return $this->sendResponse([
            'sawing_mission' => $sawing_mission,
            'rotations' => $rotations->groupBy('type'),
            'total_per_type' => $total_per_type,
            'total_per_person' => $total_per_person
        ]);
    }
}
