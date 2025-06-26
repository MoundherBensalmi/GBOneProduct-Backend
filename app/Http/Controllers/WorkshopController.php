<?php

namespace App\Http\Controllers;

use App\Models\PayPeriod;
use App\Models\Person;
use App\Models\SawingMission;
use App\Models\SawingStation;
use App\Models\SortingMission;
use App\Models\SortingRotation;
use App\Services\SawingMissionServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WorkshopController extends Controller
{
    private SawingMissionServices $sawingMissionServices;

    public function __construct(SawingMissionServices $sawingMissionServices)
    {
        $this->sawingMissionServices = $sawingMissionServices;
    }

    public function production_people(): JsonResponse
    {
        $people = Person::withTrashed()
            ->where('current_position_id', 2)
            ->get();
        return $this->sendResponse([
            'people' => $people,
        ]);
    }

    public function sawing_stations(): JsonResponse
    {
        $sawing_stations = SawingStation::query()->get();
        return $this->sendResponse([
            'sawing_stations' => $sawing_stations,
        ]);
    }

    public function my_missions(Request $request): JsonResponse
    {
        $pay_period = PayPeriod::query()->where('is_active', 1)->first();
        if (!$pay_period) {
            return $this->sendError('no_active_pay_period');
        }

        $sawing_missions = $pay_period->sawingMissions()
            ->where('assigned_user_id', $request->user()->id)
            ->where('is_finished', 0)
            ->get();

        $sorting_missions = $pay_period->sortingMissions()
            ->where('assigned_user_id', $request->user()->id)
            ->where('status', 'new')
            ->get();

        return $this->sendResponse([
            'sawing_missions' => $sawing_missions,
            'sorting_missions' => $sorting_missions,
        ]);
    }

    public function close_sawing_mission(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mission_id' => 'required',
            'rotations' => 'required|json',
            'mission_db' => 'required|file',
        ]);

        $mission = SawingMission::query()
            ->where('id', $validated['mission_id'])
            ->where('is_finished', false)
            ->where('assigned_user_id', $request->user()->id)
            ->first();
        if (!$mission) {
            return $this->sendError("المهمة غير موجودة أو منتهية");
        }

        try {
            $mission_path = 'sawing_missions/' . $validated['mission_id'] . '/' . now()->format('Y-m-d_H-i-s') . '/';
            $request->file('mission_db')->store($mission_path);

            $rotations = json_decode($validated['rotations'], true);
            if ($rotations) {
                $jsonFileName = $mission_path . 'rotations_' . now()->timestamp . '.json';
                Storage::disk('public')->put($jsonFileName, json_encode($rotations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            DB::transaction(function () use ($rotations, $mission) {
                foreach ($rotations as $rotation) {
                    $people_ids = array_column($rotation['sawingRotationPeople'], 'person_id');
                    $this->sawingMissionServices->create_rotation(
                        stationId: $rotation['sawing_station_id'],
                        missionId: $rotation['sawing_mission_id'],
                        peopleIds: $people_ids,
                        type: $rotation['type'],
                        amount: $rotation['amount'],
                        createdAt: $rotation['created_at'],
                        updatedAt: $rotation['updated_at'],
                        load: false
                    );
                }
                $mission->update([
                    'is_started' => true,
                    'is_finished' => true,
                ]);
            });
        } catch (Exception $e) {
            Log::error('Rotation creation failed: ' . $e->getMessage());
            return $this->sendError("خطأ أثناء رفع المهمة");
        }

        return $this->sendResponse("done", 'تم تحميل المهمة بنجاح');
    }

    public function close_sorting_mission(Request $request)
    {
        $validated = $request->validate([
            'mission_id' => 'required',
            'rotations' => 'required|json',
            'mission_db' => 'required|file',
        ]);

        $sorting_mission = SortingMission::query()
            ->where('id', $validated['mission_id'])
            ->where('status', "!=", 'finished')
            ->where('assigned_user_id', $request->user()->id)
            ->first();

        if (!$sorting_mission) {
            return $this->sendError("المهمة غير موجودة أو منتهية");
        }

        try {
            $now = now();
            $mission_path = 'sorting_missions/' . $validated['mission_id'] . '/' . $now->format('Y-m-d_H-i-s') . '/';

            $request->file('mission_db')->store($mission_path);

            $rotations = json_decode($validated['rotations'], true);

            if ($rotations) {
                $jsonFileName = $mission_path . 'rotations_' . $now->timestamp . '.json';
                Storage::disk('public')->put($jsonFileName, json_encode($rotations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            $insertData = array_map(fn($rotation) => [
                'sorting_mission_id' => $rotation['sorting_mission_id'],
                'person_id' => $rotation['person_id'],
                'type' => $rotation['type'],
                'amount' => $rotation['amount'],
                'created_at' => $rotation['created_at'],
                'updated_at' => $rotation['updated_at'],
            ], $rotations);

            DB::transaction(function () use ($insertData, $sorting_mission) {
                SortingRotation::query()->insert($insertData);
                $sorting_mission->update(['status' => 'finished']);
            });

        } catch (Exception $e) {
            Log::error('Rotation creation failed', ['error' => $e]);
            return $this->sendError("خطأ أثناء رفع المهمة");
        }

        return $this->sendResponse("done", 'تم تحميل المهمة بنجاح');

    }
}
