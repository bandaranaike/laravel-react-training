<?php
namespace App\Repositories;
use App\Contracts\EmployeeDirectory;
use App\Models\Employee;
class EloquentEmployeeDirectory implements EmployeeDirectory
{
    public function findByEmail(string $email, int $branchId): ?Employee
    {
        return Employee::query()->where('branch_id', $branchId)->where('email', $email)->first();
    }
    public function create(array $data, int $branchId): Employee
    {
        $employee = new Employee($data);
        $employee->branch_id = $branchId;
        $employee->save();
        return $employee;
    }
}

