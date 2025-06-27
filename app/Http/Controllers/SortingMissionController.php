<?php

namespace App\Http\Controllers;

use App\Models\SortingMission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SortingMissionController extends Controller
{
    public function show($id): JsonResponse
    {
        $sorting_mission = SortingMission::query()
            ->where('id', $id)
            ->with([
                'payPeriod',
                'responsible'
            ])
            ->first();

        if (!$sorting_mission || !$sorting_mission->payPeriod) {
            return $this->sendError("not_found");
        }

        $total_per_person_per_type = $sorting_mission->sortingRotations()
            ->join('people', 'sorting_rotations.person_id', '=', 'people.id')
            ->select([
                'people.id',
                'people.name',
                'people.tr_name',
                'type',
                DB::raw('SUM(amount) as total')
            ])
            ->groupBy('people.id', 'people.name', 'people.tr_name', 'type')
            ->get();

        $total_per_person = [];
        foreach ($total_per_person_per_type as $row) {
            if (!isset($total_per_person[$row->id])) {
                $total_per_person[$row->id] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'tr_name' => $row->tr_name,
                    'trimming' => 0,
                    'white_sorting' => 0,
                    'yellow_sorting' => 0,
                ];
            }
            $total_per_person[$row->id][$row->type] = $row->total;
        }
        $total_per_person = array_values($total_per_person);

        return $this->sendResponse([
            'sorting_mission' => $sorting_mission,
            'total_per_person' => $total_per_person
        ]);
    }

    public function update_status($id): JsonResponse
    {
        $validated = request()->validate([
            'status' => 'required|in:new,ready,finished',
        ]);

        $sorting_mission = SortingMission::query()->find($id);
        if (!$sorting_mission || !$sorting_mission->payPeriod) {
            return $this->sendError("not_found");
        }

        $sorting_mission->update([
            'status' => $validated['status'],
        ]);

        return $this->sendResponse("done");
    }
}
