<?php
namespace App\Services;
use App\Contracts\EmployeeDirectory;
use App\Events\EmployeeCreated;
use App\Exceptions\EmployeeDeleteBlocked;
use App\Exceptions\StaleEmployeeVersion;
use App\Models\AuditEntry;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
class EmployeeService
{
    public function __construct(private readonly EmployeeDirectory $directory) {}
    public function create(array $data, User $actor, string $requestId): Employee
    {
        Gate::forUser($actor)->authorize('create', Employee::class);
        return DB::transaction(function () use ($data, $actor, $requestId) {
            $employee = $this->directory->create(
                Arr::only($data, ['name', 'email', 'department_id', 'position']), $actor->branch_id
            );
            $this->audit($actor, $employee, 'employee.created', null, $requestId);
            EmployeeCreated::dispatch($employee->id, $actor->id);
            return $employee->load('department');
        });
    }
    public function update(Employee $employee, array $data, User $actor, string $requestId): Employee
    {
        return $this->mutate($employee, $data, $actor, $requestId, false);
    }
    public function changeStatus(Employee $employee, array $data, User $actor, string $requestId): Employee
    {
        return $this->mutate($employee, $data, $actor, $requestId, true);
    }
    private function mutate(Employee $employee, array $data, User $actor, string $requestId, bool $statusOnly): Employee
    {
        return DB::transaction(function () use ($employee, $data, $actor, $requestId, $statusOnly) {
            $current = Employee::query()->lockForUpdate()->findOrFail($employee->id);
            Gate::forUser($actor)->authorize($statusOnly ? 'changeStatus' : 'update', $current);
            if ($current->version !== (int) $data['version']) { throw new StaleEmployeeVersion(); }
            $before = $this->snapshot($current);
            $current->fill(Arr::only($data, $statusOnly ? ['status'] : ['name', 'email', 'department_id', 'position']));
            $current->version++;
            $current->save();
            $this->audit($actor, $current, $statusOnly ? 'employee.status_changed' : 'employee.updated', $before, $requestId);
            return $current->load('department');
        });
    }
    public function delete(Employee $employee, User $actor, string $requestId): void
    {
        DB::transaction(function () use ($employee, $actor, $requestId) {
            $current = Employee::query()->lockForUpdate()->findOrFail($employee->id);
            Gate::forUser($actor)->authorize('delete', $current);
            // This workshop retains employees referenced by ANY payroll record.
            if (DB::table('payroll_entries')->where('employee_id', $current->id)->exists()) {
                throw new EmployeeDeleteBlocked();
            }
            $before = $this->snapshot($current);
            $current->delete();
            $this->audit($actor, $current, 'employee.deleted', $before, $requestId, true);
        });
    }
    private function snapshot(Employee $employee): array
    {
        return $employee->only(['name', 'email', 'department_id', 'position', 'status', 'version']);
    }
    private function audit(User $actor, Employee $employee, string $action, ?array $before, string $requestId, bool $deleted = false): void
    {
        AuditEntry::create(['actor_id' => $actor->id, 'branch_id' => $actor->branch_id,
            'action' => $action, 'subject_type' => Employee::class, 'subject_id' => (string) $employee->id,
            'before_values' => $before, 'after_values' => $deleted ? null : $this->snapshot($employee),
            'request_id' => $requestId, 'created_at' => now()]);
    }
}

