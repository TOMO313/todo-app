<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('tasks/index');
})->middleware('auth')->name('dashboard');

Route::controller(TaskController::class)->middleware('auth')->group(function () {
    Route::post('/task/store', 'taskStore')->name('task.store');
    Route::post('/task/get', 'getTask')->name('task.get');
    Route::put('/task/update', 'updateTask')->name('task.update');
    Route::delete('/task/delete', 'deleteTask')->name('task.delete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
