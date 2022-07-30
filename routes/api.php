<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("v1")->group(function (): void {
    Route::prefix("admin")->group(function (): void {
        Route::post("create", [AdminController::class, "store"]);
        Route::post("login", [AdminController::class, "login"]);
        Route::get("logout", [AdminController::class, "logout"]);
        Route::get("user-listing", [AdminController::class, "listUsers"]);
        Route::put("user-edit/{uuid}", [AdminController::class, "update"]);
        Route::delete("user-delete/{uuid}", [AdminController::class, "destroy"]);
    });
});
