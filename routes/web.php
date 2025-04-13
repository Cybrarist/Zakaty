<?php

use Illuminate\Support\Facades\Route;

Route::get("storage/{path}/{filename}", \App\Http\Controllers\HandleStorageController::class)
    ->name('storage.local')
    ->middleware('auth');
