<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ChangeEmployeeStatusRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('changeStatus', $this->route('employee')); }
    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['active', 'inactive'])],
            'version' => ['required', 'integer', 'min:1']];
    }
}

