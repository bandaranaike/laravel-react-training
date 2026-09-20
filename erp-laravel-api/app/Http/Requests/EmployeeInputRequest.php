<?php
namespace App\Http\Requests;
use App\Models\Employee;
use App\Rules\CompanyEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
abstract class EmployeeInputRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }
    protected function employeeRules(?Employee $employee = null): array
    {
        $unique = Rule::unique('employees', 'email');
        if ($employee) { $unique->ignore($employee); }
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['bail', 'required', 'email', 'max:254',
                new CompanyEmail(config('employees.allowed_email_domains')), $unique],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->where('active', true)],
            'position' => ['required', 'string', 'max:100'],
            'branch_id' => ['prohibited'], 'role' => ['prohibited'],
            'status' => ['prohibited'], // Dedicated manager-only status endpoint.
        ];
    }
}

