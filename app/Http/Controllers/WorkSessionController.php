<?php

namespace App\Http\Controllers;

use App\Models\PayPeriod;
use App\Models\WorkSession;
use Illuminate\Http\JsonResponse;

class WorkSessionController extends Controller
{
    public function index(): JsonResponse
    {
        $pay_period = PayPeriod::query()->where('is_active', 1)->first();
        if (!$pay_period) {
            return response()->json([
                'message' => 'No active pay period found',
            ], 404);
        }

        $work_sessions = $pay_period->workSessions()
            ->with(['sawingMissions'])
            ->get();

        return response()->json([
            'work_sessions' => $work_sessions,
        ]);
    }

    public function show(int $session): JsonResponse
    {
        $start_time = microtime(true);

        $work_session = WorkSession::query()
            ->whereHas('payPeriod', function ($query) {
                $query->where('is_active', 1);
            })
            ->with(['sawingMissions'])
            ->find($session);

        if (!$work_session) {
            return response()->json([
                'message' => 'Work session not found',
                'duration_ms' => round((microtime(true) - $start_time) * 1000, 2),
            ], 404);
        }

        return response()->json([
            'work_session' => $work_session,
            'duration_ms' => round((microtime(true) - $start_time) * 1000, 2),
        ]);
    }

}
