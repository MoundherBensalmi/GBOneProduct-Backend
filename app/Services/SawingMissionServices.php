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
            ->with(['payPeriod'])
            ->first();

        return [
            'is_active' => $sawing_mission &&
                $sawing_mission->payPeriod &&
                $sawing_mission->payPeriod->is_active,
            'sawing_mission' => $sawing_mission
        ];
    }

    public function init_station(int $station_id, int $mission_id, array $people): SawingRotation
    {
        $initial_rotation = SawingRotation::query()->firstOrCreate([
            'type' => 'initial',
            'sawing_station_id' => $station_id,
            'sawing_mission_id' => $mission_id,
        ]);
        $initial_rotation->people()->sync($people);

        return $initial_rotation->load('people');
    }

    public function create_rotation(int $stationId, int $missionId, array $peopleIds, string $type, float $amount, ?string $createdAt = null, ?string $updatedAt = null, bool $load = true): SawingRotation
    {
        $now = now();
        $rotation = SawingRotation::query()->create([
            'type' => $type,
            'sawing_station_id' => $stationId,
            'sawing_mission_id' => $missionId,
            'amount' => $amount,
            'created_at' => $createdAt ?? $now,
            'updated_at' => $updatedAt ?? $now,
        ]);

        $this->syncPeopleWithAmount($rotation, $peopleIds, $amount, $createdAt, $updatedAt, false);

        if ($load) {
            $rotation->load('people');
        }
        return $rotation;
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

    private function syncPeopleWithAmount(SawingRotation $rotation, array $peopleIds, float $totalAmount, ?string $createdAt = null, ?string $updatedAt = null, bool $emptyBeforeSync = true): void
    {
        // remove old people from rotation
        if ($emptyBeforeSync) $rotation->people()->sync([]);

        if (empty($peopleIds)) return;

        $now = now();
        $splitAmount = round($totalAmount / count($peopleIds), 2);

        $data = array_map(function ($personId) use ($rotation, $splitAmount, $createdAt, $updatedAt, $now) {
            return [
                'sawing_rotation_id' => $rotation->getAttribute('id'),
                'person_id' => $personId,
                'amount' => $splitAmount,
                'created_at' => $createdAt ?? $now,
                'updated_at' => $updatedAt ?? $now,
            ];
        }, $peopleIds);

        DB::table('sawing_rotation_person')->insert($data);
    }
}
