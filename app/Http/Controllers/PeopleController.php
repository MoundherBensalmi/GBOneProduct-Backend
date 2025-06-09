<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function production(): JsonResponse
    {
        $people = Person::withTrashed()
            ->where('current_position_id', 2)
            ->get();
        return $this->sendResponse([
            'people' => $people,
        ]);
    }
}
