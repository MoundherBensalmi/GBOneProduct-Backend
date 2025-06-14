<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function index(): JsonResponse
    {
        $positions = Position::query()
            ->orderBy('name', 'asc')
            ->get();
        return $this->sendResponse($positions);
    }
}
