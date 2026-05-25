<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubTaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('tasks.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('tasks.toggle');
    Route::patch('/tasks/{task}/update-order', [TaskController::class, 'updateOrder'])->name('tasks.updateOrder');
    Route::patch('/tasks/{task}/update-deadline', [TaskController::class, 'updateDeadline'])->name('tasks.updateDeadline');

    Route::post('/sub-tasks', [SubTaskController::class, 'store'])->name('sub-tasks.store');
    Route::patch('/sub-tasks/{subTask}/toggle', [SubTaskController::class, 'toggleComplete'])->name('sub-tasks.toggle');
    Route::delete('/sub-tasks/{subTask}', [SubTaskController::class, 'destroy'])->name('sub-tasks.destroy');

    Route::get('/calendar-data', [TaskController::class, 'calendarData'])->name('calendar.data');
    Route::get('/export/json', [TaskController::class, 'exportJson'])->name('export.json');
    Route::post('/import/json', [TaskController::class, 'importJson'])->name('import.json');
});

require __DIR__ . '/auth.php';
