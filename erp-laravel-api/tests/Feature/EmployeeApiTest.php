<?php
use App\Jobs\ExportEmployees;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
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
it('serves the public hello route', function () {
    $this->getJson('/api/hello')->assertOk()->assertExactJson(['message' => 'Hello from Laravel']);
});
it('returns the authenticated user and active departments', function () {
    $user = employeeUser('viewer');
    $active = Department::factory()->create(['name' => 'Active HR', 'active' => true]);
    Department::factory()->create(['name' => 'Inactive HR', 'active' => false]);
    Sanctum::actingAs($user, ['employees:read']);

    $this->getJson('/api/me')->assertOk()->assertJsonPath('data.id', $user->id);
    $this->getJson('/api/departments')->assertOk()->assertJsonPath('data.0.id', $active->id)
        ->assertJsonMissing(['name' => 'Inactive HR']);
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
it('updates an employee and changes status only for a manager', function () {
    $branch = Branch::factory()->create();
    $department = Department::factory()->create();
    $user = employeeUser('hr_manager', $branch);
    $employee = Employee::factory()->create(['branch_id' => $branch->id, 'department_id' => $department->id]);
    Sanctum::actingAs($user, ['employees:write']);

    $this->putJson('/api/employees/'.$employee->id, [
        'name' => 'Updated Name', 'email' => 'updated@company.test',
        'department_id' => $department->id, 'position' => 'Manager', 'version' => $employee->version,
    ])->assertOk()->assertJsonPath('data.name', 'Updated Name');

    $this->patchJson('/api/employees/'.$employee->id.'/status', [
        'status' => 'inactive', 'version' => 2,
    ])->assertOk()->assertJsonPath('data.status', 'inactive');
});
it('blocks deletion when payroll entries exist', function () {
    $branch = Branch::factory()->create();
    $user = employeeUser('hr_manager', $branch);
    $employee = Employee::factory()->create(['branch_id' => $branch->id]);
    DB::table('payroll_entries')->insert([
        'employee_id' => $employee->id, 'status' => 'draft', 'created_at' => now(), 'updated_at' => now(),
    ]);
    Sanctum::actingAs($user, ['employees:write']);

    $this->deleteJson('/api/employees/'.$employee->id)->assertConflict()
        ->assertJsonPath('code', 'EMPLOYEE_DELETE_BLOCKED');
});
it('queues an export for an authorized user', function () {
    Queue::fake(); Storage::fake('local'); $user = employeeUser(); Sanctum::actingAs($user, ['employees:export']);
    $response = $this->postJson('/api/employee-exports')->assertAccepted()->assertJsonPath('data.status', 'queued');
    $exportId = $response->json('data.id');
    Queue::assertPushed(ExportEmployees::class);
    $this->getJson('/api/employee-exports/'.$exportId)->assertOk()->assertJsonPath('data.id', $exportId);
    $this->getJson('/api/employee-exports/'.$exportId.'/download')->assertNotFound();
});
