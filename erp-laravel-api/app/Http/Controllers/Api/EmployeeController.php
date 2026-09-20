<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeEmployeeStatusRequest;
use App\Http\Requests\IndexEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $employees) {}
    public function index(IndexEmployeeRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $query = Employee::query()->with('department')->where('branch_id', $request->user()->branch_id);
        foreach (['department_id', 'status'] as $field) {
            if (isset($filters[$field])) { $query->where($field, $filters[$field]); }
        }
        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = $filters['search'];
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
        }
        $sort = $filters['sort'] ?? 'id';
        $query->orderBy($sort, $filters['direction'] ?? 'asc');
        if ($sort !== 'id') { $query->orderBy('id'); }
        return EmployeeResource::collection($query->paginate((int) ($filters['per_page'] ?? 20))->withQueryString());
    }
    public function show(Employee $employee): EmployeeResource
    {
        Gate::authorize('view', $employee);
        return new EmployeeResource($employee->load('department'));
    }
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        return (new EmployeeResource($this->employees->create($request->validated(), $request->user(),
            $request->attributes->get('request_id'))))->response()->setStatusCode(201);
    }
    public function update(UpdateEmployeeRequest $request, Employee $employee): EmployeeResource
    {
        return new EmployeeResource($this->employees->update($employee, $request->validated(),
            $request->user(), $request->attributes->get('request_id')));
    }
    public function changeStatus(ChangeEmployeeStatusRequest $request, Employee $employee): EmployeeResource
    {
        return new EmployeeResource($this->employees->changeStatus($employee, $request->validated(),
            $request->user(), $request->attributes->get('request_id')));
    }
    public function destroy(Request $request, Employee $employee): Response
    {
        $this->employees->delete($employee, $request->user(), $request->attributes->get('request_id'));
        return response()->noContent();
    }
}

