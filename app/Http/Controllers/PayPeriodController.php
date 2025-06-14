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


}
