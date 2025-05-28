<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function index(): JsonResponse
    {
        $people = Person::query()->get();
        return response()->json([
            'people' => $people,
        ]);
    }
}
