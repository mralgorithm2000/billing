<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


//Auth
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);


