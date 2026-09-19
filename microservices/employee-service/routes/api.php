<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/employees', [
    EmployeeController::class,
    'index'
]);

Route::get('/employees/{id}', [
    EmployeeController::class,
    'show'
]);
