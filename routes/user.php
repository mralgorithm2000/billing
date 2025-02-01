<?php

use App\Http\Controllers\Api\TransactionsController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


//Auth
Route::post("/transaction/deposit", [TransactionsController::class, "deposit"]);
Route::post("/transaction/withdraw", [TransactionsController::class, "withdraw"]);

Route::get("/transaction/history", [TransactionsController::class, "showHistory"]);
Route::get("/info", [UserController::class, "info"]);

