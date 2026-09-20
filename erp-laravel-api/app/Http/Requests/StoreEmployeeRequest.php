<?php
namespace App\Http\Requests;
use App\Models\Employee;
class StoreEmployeeRequest extends EmployeeInputRequest
{
    public function authorize(): bool { return $this->user()->can('create', Employee::class); }
    public function rules(): array { return $this->employeeRules(); }
}

