<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\StaffController;

Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-schedule', [StaffController::class, 'mySchedule'])->name('my-schedule');
    Route::get('/my-schedule/api/weekly', [StaffController::class, 'getMyWeeklySchedule'])->name('my-schedule.api');
});