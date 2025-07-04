<?php

namespace App\Http\Controllers;

use App\Enums\PositionType;
use App\Http\Requests\StorePersonRequest;
use App\Models\Person;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeopleController extends Controller
{
    public function mission_users(): JsonResponse
    {
        $users = Person::query()
            ->where('current_position_id', PositionType::PRODUCTION)
            ->whereNotNull('username')
            ->whereNotNull('password')
            ->get();
        return $this->sendResponse([
            'users' => $users,
        ], 'mission_users_retrieved_successfully.');
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $onlyUsers = $request->input('only_users', false);

        $isAdmin = $request->user()->role === 'admin';

        $people = Person::query()->with('currentPosition');

        if (!$isAdmin) {
            $people->where('role', '!=', 'admin');
        }

        if ($search) {
            $people->where(function ($query) use ($search) {
                $query->where('id', 'ilike', '%' . $search . '%')
                    ->orWhere('name', 'ilike', '%' . $search . '%')
                    ->orWhere('tr_name', 'ilike', '%' . $search . '%')
                    ->orWhere('phone', 'ilike', '%' . $search . '%')
                    ->orWhereHas('currentPosition', function ($query) use ($search) {
                        $query->where('name', 'ilike', '%' . $search . '%')
                            ->orWhere('tr_name', 'ilike', '%' . $search . '%');
                    });
            });
        }

        if ($onlyUsers) {
            $people->whereNotNull(['username', 'password']);
        }

        $people = $people->orderBy('name')->paginate();

        $peopleCountQuery = Person::query();
        $usersCountQuery = Person::query()->whereNotNull(['username', 'password']);

        if (!$isAdmin) {
            $peopleCountQuery->where('role', '!=', 'admin');
            $usersCountQuery->where('role', '!=', 'admin');
        }

        $peopleCount = $peopleCountQuery->count();
        $usersCount = $usersCountQuery->count();

        return $this->sendResponse([
            'people' => $people,
            'people_count' => $peopleCount,
            'users_count' => $usersCount,
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
        ]);

        $query = Person::withTrashed()->with('currentPosition');

        $query->where('name', 'ilike', '%' . $validated['name'] . '%')
            ->orWhere('tr_name', 'ilike', '%' . $validated['name'] . '%');
        $people = $query->orderBy('name')->limit(10)->get();

        return response()->json([
            'people' => $people,
            'total' => $people->count(),
        ]);
    }

    public function store(StorePersonRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $willCreateUser = isset($validated['create_user']) && $validated['create_user'];

        $user = $request->user();
        if ($user->role !== 'admin' && $willCreateUser && $validated['role'] === 'admin') {
            return $this->sendError('unauthorized_to_create_admin_users.', [], 403);
        }
        DB::beginTransaction();
        try {
            $new_person_data = [
                'name' => $validated['name'],
                'tr_name' => $validated['tr_name'],
                'phone' => $validated['phone'],
                'current_position_id' => $validated['current_position_id'],
            ];

            if ($willCreateUser) {
                $new_person_data['username'] = $validated['username'];
                $new_person_data['password'] = bcrypt($validated['password']);
                $new_person_data['role'] = $validated['role'];
            }

            Person::query()->create($new_person_data);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendError('failed_to_create_person.', ['error' => $e->getMessage()], 500);
        }

        return $this->sendResponse("done", 'person_created_successfully.');
    }

    public function recover($id): JsonResponse
    {
        $person = Person::withTrashed()->findOrFail($id);

        if ($person->trashed()) {
            $person->restore();
        }

        return $this->sendResponse("done", 'person_recovered_successfully.');
    }
}
