<?php

namespace App\Http\Controllers;

use App\Models\SawingMission;
use App\Services\SawingMissionServices;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->first();
        if (!$mission) {
            return $this->sendError("المهمة غير موجودة أو منتهية");
        }

        $mission_path = 'sawing_missions/' . $validated['mission_id'] . '/' . now()->format('Y-m-d_H-i-s') . '/';
        $request->file('mission_db')->store($mission_path, 's3');

        $rotations = json_decode($validated['rotations'], true);
        if ($rotations) {
            $jsonFileName = $mission_path . 'rotations_' . now()->timestamp . '.json';
            Storage::disk('s3')->put($jsonFileName, json_encode($rotations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        try {
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
}
