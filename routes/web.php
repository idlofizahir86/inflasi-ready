<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\ApiSettingsController;
use App\Http\Controllers\SupportController;



Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/data-center', [CommodityController::class, 'index'])->name('datacenter');
Route::post('/data-center', [CommodityController::class, 'store'])->name('datacenter.store');

Route::get('/simulation', [SimulationController::class, 'index'])->name('simulation');
Route::post('/simulation/calculate', [SimulationController::class, 'calculate'])->name('simulation.calculate');

Route::get('/api-settings', [ApiSettingsController::class, 'index'])->name('api-settings');

Route::get('/support', [SupportController::class, 'index'])->name('support');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
