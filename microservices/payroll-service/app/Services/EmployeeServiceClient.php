<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmployeeServiceClient
{
    public function find(int $employeeId): array
    {
        return Http::timeout(5)
            ->retry(2, 200)
            ->get(
                config('services.employee_service.url')
                . "/api/employees/{$employeeId}"
            )
            ->throw()
            ->json();
    }
}