<?php
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmployeeExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::get('/hello', fn () => response()->json(['message' => 'Hello from Laravel']));
Route::middleware(['auth:sanctum', 'active', 'throttle:employees'])->group(function () {
    Route::get('/me', fn (Request $r) => response()->json(['data' => $r->user()->only(['id', 'name', 'email', 'role', 'branch_id'])]));
    Route::middleware('abilities:employees:read')->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
    });
    Route::middleware('abilities:employees:write')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
        Route::patch('/employees/{employee}/status', [EmployeeController::class, 'changeStatus']);
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);
    });
    Route::middleware('abilities:employees:export')->group(function () {
        Route::post('/employee-exports', [EmployeeExportController::class, 'store'])->middleware('throttle:exports');
        Route::get('/employee-exports/{export}', [EmployeeExportController::class, 'show']);
        Route::get('/employee-exports/{export}/download', [EmployeeExportController::class, 'download']);
    });
});

