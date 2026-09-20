<?php
use App\Jobs\ExportEmployees;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
uses(RefreshDatabase::class);
function employeeUser(string $role = 'hr_manager', ?Branch $branch = null): User {
    return User::factory()->create(['role' => $role, 'active' => true,
        'branch_id' => $branch?->id ?? Branch::factory()->create()->id]);
}
function employeePayload(Department $department): array {
    return ['name' => 'Anne Perera', 'email' => 'anne@company.test',
        'department_id' => $department->id, 'position' => 'HR Executive'];
}
it('requires authentication', function () {
    $this->getJson('/api/employees')->assertUnauthorized();
});
it('creates an employee and records an audit entry', function () {
    $user = employeeUser(); $department = Department::factory()->create();
    Sanctum::actingAs($user, ['employees:write']);
    $this->postJson('/api/employees', employeePayload($department))->assertCreated()
        ->assertJsonPath('data.name', 'Anne Perera');
    $this->assertDatabaseHas('employees', ['email' => 'anne@company.test', 'branch_id' => $user->branch_id]);
    $this->assertDatabaseHas('audit_entries', ['actor_id' => $user->id, 'action' => 'employee.created']);
});
it('rejects an email outside the approved domain', function () {
    $user = employeeUser(); $department = Department::factory()->create();
    Sanctum::actingAs($user, ['employees:write']);
    $payload = employeePayload($department); $payload['email'] = 'anne@example.com';
    $this->postJson('/api/employees', $payload)->assertUnprocessable()->assertJsonPath('code', 'VALIDATION_FAILED');
});
it('hides another branch from the list and direct lookup', function () {
    $first = Branch::factory()->create(); $second = Branch::factory()->create();
    $user = employeeUser('viewer', $first); $other = Employee::factory()->create(['branch_id' => $second->id]);
    Sanctum::actingAs($user, ['employees:read']);
    $this->getJson('/api/employees')->assertOk()->assertJsonMissing(['id' => $other->id]);
    $this->getJson('/api/employees/'.$other->id)->assertForbidden();
});
it('prevents stale updates', function () {
    $branch = Branch::factory()->create(); $user = employeeUser('hr_manager', $branch);
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);
    Sanctum::actingAs($user, ['employees:write']);
    $this->putJson('/api/employees/'.$employee->id, [
        'name' => 'Anne Perera', 'email' => 'anne@company.test', 'department_id' => $employee->department_id,
        'position' => 'Engineer', 'version' => 99,
    ])->assertConflict()->assertJsonPath('code', 'STALE_EMPLOYEE_VERSION');
});
it('queues an export for an authorized user', function () {
    Queue::fake(); $user = employeeUser(); Sanctum::actingAs($user, ['employees:export']);
    $this->postJson('/api/employee-exports')->assertAccepted()->assertJsonPath('data.status', 'queued');
    Queue::assertPushed(ExportEmployees::class);
});

