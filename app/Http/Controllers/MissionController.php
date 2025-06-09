<?php

namespace App\Http\Controllers;

use App\Models\PayPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function mine(): JsonResponse
    {
        $pay_period = PayPeriod::query()->where('is_active', 1)->first();
        if (!$pay_period) {
            return $this->sendError('no_active_pay_period');
        }

        $user_id = Auth::guard('sanctum')->id();
        $sawing_missions = $pay_period->sawingMissions()
            ->where('assigned_user_id', $user_id)
            ->where('is_finished', 0)
            ->get();

        return $this->sendResponse([
            'sawing_missions' => $sawing_missions,
        ]);
    }
}
