<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->get("/", [DashboardController::class,"index"])->name("api.dashboard");

