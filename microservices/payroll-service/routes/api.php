<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/salary/{employeeId}', [\App\Http\Controllers\SalaryController::class, 'show']);
