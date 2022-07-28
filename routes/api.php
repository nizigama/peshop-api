<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("v1")->group(function () {

    Route::prefix("admin")->group(function () {

        Route::post("create", [AdminController::class, "store"]);
        Route::post("login", [AdminController::class, "login"]);
        Route::get("logout", [AdminController::class, "logout"])->middleware('protector:admin');
        Route::get("user-listing", [AdminController::class, "listUsers"]);
        Route::put("user-edit", [AdminController::class, "update"]);
        Route::delete("user-delete", [AdminController::class, "destroy"]);
    });
});
