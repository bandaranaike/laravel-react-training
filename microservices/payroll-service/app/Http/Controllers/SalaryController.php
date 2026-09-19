<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmployeeServiceClient;
use App\Models\Salary;

class SalaryController extends Controller
{
    public function __construct(
        private EmployeeServiceClient $employees
    ) {
    }

    public function show(int $employeeId)
    {
        $employee = $this->employees->find($employeeId);

        $salary = Salary::where('employee_id', $employeeId)->first();

        return response()->json([
            'data' => [
                'employee' => $employee,
                'salary' => $salary
            ]
        ]);
         
    }
}
