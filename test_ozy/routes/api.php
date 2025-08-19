<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToDoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ToDo routes
Route::apiResource('todo', ToDoController::class);
// complete a single ToDo
Route::put('todo/{id}/complete', [ToDoController::class, 'complete']);
// complete an array of ToDos
Route::put('complete-all', [ToDoController::class, 'completeAll']);
