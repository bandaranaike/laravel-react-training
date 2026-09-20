<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Employee::class);
        $departments = Cache::remember('departments:active', now()->addMinutes(10),
            fn () => Department::query()->where('active', true)->orderBy('name')->get(['id', 'name']));
        return response()->json(['data' => $departments]);
    }
}

