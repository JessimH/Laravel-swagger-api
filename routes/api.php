<?php

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
Route::post('auth/register', 'App\Http\Controllers\RegisterController@register');
Route::post('auth/login', 'App\Http\Controllers\LoginController@login');

Route::middleware('auth:sanctum')->get('tasks', 'App\Http\Controllers\TasksController@tasks');
Route::middleware('auth:sanctum')->get('checkTask/{id}', 'App\Http\Controllers\TasksController@checkTask');
Route::middleware('auth:sanctum')->post('addTask', 'App\Http\Controllers\TasksController@addTask');
Route::middleware('auth:sanctum')->put('updateTask/{id}', 'App\Http\Controllers\TasksController@updateTask');
Route::middleware('auth:sanctum')->delete('deleteTask/{id}', 'App\Http\Controllers\TasksController@deleteTask');

