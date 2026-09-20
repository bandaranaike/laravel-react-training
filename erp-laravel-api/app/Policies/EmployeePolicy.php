<?php
namespace App\Policies;
use App\Models\Employee;
use App\Models\User;
class EmployeePolicy
{
    public function viewAny(User $user): bool { return $user->canReadEmployees(); }
    public function view(User $user, Employee $employee): bool
    {
        return $user->canReadEmployees() && $user->branch_id === $employee->branch_id;
    }
    public function create(User $user): bool { return $user->canWriteEmployees(); }
    public function update(User $user, Employee $employee): bool
    {
        return $user->canWriteEmployees() && $user->branch_id === $employee->branch_id;
    }
    public function delete(User $user, Employee $employee): bool
    {
        return $this->update($user, $employee) && $user->role === 'hr_manager';
    }
    public function changeStatus(User $user, Employee $employee): bool
    {
        return $this->delete($user, $employee);
    }
}

