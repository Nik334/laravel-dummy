<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthenticateUser;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


//role
Route::post('createRole', [RoleController::class, 'createRole']);
Route::post('getRole', [RoleController::class, 'getRole']);
Route::post('updateRole', [RoleController::class, 'updateRole']);

//department
Route::post('createDepartment', [DepartmentController::class,'createDepartment']);
Route::post('getDepartment', [DepartmentController::class,'getDepartment']);


Route::middleware([AuthenticateUser::class])->group(function () {
Route::get('self', [AuthController::class, 'self']);
Route::get('logout', [AuthController::class, 'logout']);



});
