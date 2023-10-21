<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TestController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('whatsapp/send-message', [TestController::class, 'sendMessages']);
Route::get('whatsapp/webhook', [TestController::class, 'verifyWebhook']);
Route::post('whatsapp/webhook', [TestController::class, 'processWebhook']);

Route::resource('messages', MessageController::class);

Route::resource('departments', DepartmentController::class);
Route::resource('employees', EmployeeController::class);
Route::get('employeesall', [EmployeeController::class, 'all']);
Route::get('employeesbydepartment', [EmployeeController::class, 'EmployeesByDepartment']);
