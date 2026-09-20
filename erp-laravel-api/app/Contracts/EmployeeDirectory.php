<?php
namespace App\Contracts;
use App\Models\Employee;
interface EmployeeDirectory
{
    public function findByEmail(string $email, int $branchId): ?Employee;
    public function create(array $data, int $branchId): Employee;
}

