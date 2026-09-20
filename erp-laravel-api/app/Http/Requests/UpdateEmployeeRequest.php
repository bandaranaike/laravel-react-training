<?php
namespace App\Http\Requests;
class UpdateEmployeeRequest extends EmployeeInputRequest
{
    public function authorize(): bool { return $this->user()->can('update', $this->route('employee')); }
    public function rules(): array
    {
        return $this->employeeRules($this->route('employee')) + [
            'version' => ['required', 'integer', 'min:1'],
        ];
    }
}

