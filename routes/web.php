<?php

use Illuminate\Support\Facades\Route;

Route::get("storage/{path}/{filename}", \App\Http\Controllers\HandleStorageController::class)
    ->name('storage.local')
    ->middleware('auth');

Route::get('/temp', function (){
    return redirect()->route('filament.admin.auth.login');
})->name('login');
