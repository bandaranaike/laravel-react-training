<?php
namespace Database\Factories;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;
    public function definition(): array
    {
        return ['branch_id' => Branch::factory(), 'department_id' => Department::factory(),
            'name' => fake()->name(), 'email' => fake()->unique()->safeEmail(),
            'position' => fake()->jobTitle(), 'status' => 'active', 'version' => 1];
    }
}

