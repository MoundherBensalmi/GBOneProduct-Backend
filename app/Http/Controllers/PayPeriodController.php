<?php

namespace App\Http\Controllers;

use App\Models\PayPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayPeriodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $only_active = $request->input('only_active', false) === 'true';
        $pay_periods = PayPeriod::query()
            ->orderBy('start_date', 'desc');
        if ($only_active) {
            $pay_periods->where('is_active', true);
        }
        $pay_periods = $pay_periods->paginate();
        return $this->sendResponse([
            'pay_periods' => $pay_periods,
        ]);
    }

    public function show($id): JsonResponse
    {
        $pay_period = PayPeriod::query()->findOrFail($id);

        $grouped_missions = [];

        $sawing_missions = $pay_period->sawingMissions()
            ->with([
                'assignedUser' => function ($query) {
                    $query->withTrashed()->with([
                        'person' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                }
            ])
            ->orderBy('date')
            ->get();
        foreach ($sawing_missions as $mission) {
            $grouped_missions[$mission->date]['sawing_missions'][] = $mission;
        }

        $sorting_missions = $pay_period->sortingMissions()
            ->with([
                'assignedUser' => function ($query) {
                    $query->withTrashed()->with([
                        'person' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                }
            ])
            ->orderBy('date')
            ->get();
        foreach ($sorting_missions as $mission) {
            $grouped_missions[$mission->date]['sorting_missions'][] = $mission;
        }

        return $this->sendResponse([
            'pay_period' => $pay_period,
            'grouped_missions' => $grouped_missions,
        ]);
    }
}
