<?php

namespace App\Services;

use App\Models\SawingMission;
use App\Models\SawingRotation;
use App\Models\SawingStation;
use Illuminate\Support\Facades\DB;

class SawingMissionServices
{
    public function is_active(int $mission_id): array
    {
        $sawing_mission = SawingMission::query()
            ->where('id', $mission_id)
            ->with(['workSession', 'workSession.payPeriod'])
            ->first();

        return [
            'is_active' => $sawing_mission &&
                $sawing_mission->workSession &&
                $sawing_mission->workSession->is_active &&
                $sawing_mission->workSession->payPeriod &&
                $sawing_mission->workSession->payPeriod->is_active,
            'sawing_mission' => $sawing_mission
        ];
    }

    public function init_station(int $station_id, int $mission_id, array $people): SawingRotation
    {
        $initial_rotation = SawingRotation::query()->firstOrCreate([
            'is_initial' => true,
            'sawing_station_id' => $station_id,
            'sawing_mission_id' => $mission_id,
        ]);
        $initial_rotation->people()->sync($people);

        return $initial_rotation->load('people');
    }

    public function create_rotation(int $stationId, int $missionId, array $peopleIds, float $amount): SawingRotation
    {
        $rotation = SawingRotation::query()->create([
            'is_initial' => false,
            'sawing_station_id' => $stationId,
            'sawing_mission_id' => $missionId,
            'amount' => $amount,
        ]);

        $this->syncPeopleWithAmount($rotation, $peopleIds, $amount);

        return $rotation->load('people');
    }

    public function update_rotation(int $rotationId, array $peopleIds, float $amount): SawingRotation
    {
        $rotation = SawingRotation::query()->findOrFail($rotationId);

        $rotation->update(['amount' => $amount]);
        $this->syncPeopleWithAmount($rotation, $peopleIds, $amount);

        return $rotation->load('people');
    }

    public function remove_rotation(int $rotationId): void
    {
        DB::transaction(function () use ($rotationId) {
            $rotation = SawingRotation::query()->findOrFail($rotationId);
            $rotation->people()->sync([]);
            $rotation->delete();
        });
    }

    // -------------------------------------------------------------------
    // ------------------------ private functions ------------------------
    // -------------------------------------------------------------------

    private function syncPeopleWithAmount(SawingRotation $rotation, array $peopleIds, float $totalAmount): void
    {
        if (empty($peopleIds)) {
            $rotation->people()->sync([]);
            return;
        }

        $splitAmount = round($totalAmount / count($peopleIds), 2);
        $syncData = array_fill_keys($peopleIds, ['amount' => $splitAmount]);

        $rotation->people()->sync($syncData);
    }
}
