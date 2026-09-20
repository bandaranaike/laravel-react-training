<?php
namespace Database\Seeders;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(['name' => 'Colombo']);
        $department = Department::firstOrCreate(['name' => 'IT'], ['active' => true]);
        $password = config('workshop.seed_password');
        if (! is_string($password) || $password === '') {
            throw new \RuntimeException('Set WORKSHOP_SEED_PASSWORD before running the workshop seeder.');
        }
        foreach (['viewer', 'hr_officer', 'hr_manager'] as $role) {
            User::updateOrCreate(['email' => "$role@company.test"], [
                'name' => ucwords(str_replace('_', ' ', $role)), 'password' => Hash::make($password),
                'role' => $role, 'branch_id' => $branch->id, 'active' => true,
            ]);
        }
        Employee::firstOrCreate(['email' => 'john@company.test'], [
            'name' => 'John Silva', 'branch_id' => $branch->id, 'department_id' => $department->id,
            'position' => 'Software Engineer', 'status' => 'active',
        ]);
    }
}
