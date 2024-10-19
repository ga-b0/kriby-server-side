<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'getAllUsers']);
Route::post('/users/create', [UserController::class, 'createUser']);

?>