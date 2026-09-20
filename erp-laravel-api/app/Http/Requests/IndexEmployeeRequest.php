<?php
namespace App\Http\Requests;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class IndexEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('viewAny', Employee::class); }
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'sort' => ['sometimes', Rule::in(['id', 'name', 'email'])],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}

