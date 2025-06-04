<?php

namespace App\Http\Controllers;

use App\Models\PayPeriod;
use Illuminate\Http\JsonResponse;

class MissionController extends Controller
{
    public function index(): JsonResponse
    {
        $pay_period = PayPeriod::query()->where('is_active', 1)->first();
        if (!$pay_period) {
            return $this->sendError('no_active_pay_period');
        }

        $sawing_missions = $pay_period->sawingMissions()->get();

        return $this->sendResponse([
            'sawing_missions' => $sawing_missions,
        ]);
    }
}
