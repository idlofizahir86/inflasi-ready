<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommodityController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ApiSettingsController;
use App\Http\Controllers\SupportController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/data-center', [CommodityController::class, 'index'])->name('datacenter');
Route::post('/data-center', [CommodityController::class, 'store'])->name('datacenter.store');
Route::get('/api/commodities', [CommodityController::class, 'list'])->name('commodities.list');

// Simulation Routes - Enhanced dengan AI & Supplier Matching
Route::get('/simulation', [SimulationController::class, 'index'])->name('simulation');
Route::post('/simulation/calculate', [SimulationController::class, 'calculate'])->name('simulation.calculate');

// NEW: Advanced Simulation Features (integrated dalam SimulationController)
Route::post('/simulation/advanced-calculate', [SimulationController::class, 'advancedCalculate'])->name('simulation.advanced-calculate');
Route::post('/simulation/extract-story', [SimulationController::class, 'extractFromStory'])->name('simulation.extract-story');
Route::post('/simulation/chat', [SimulationController::class, 'chat'])->name('simulation.chat');
Route::get('/simulation/suppliers', [SimulationController::class, 'getSupplierRecommendations'])->name('simulation.suppliers');
Route::post('/simulation/simulate-with-supplier', [SimulationController::class, 'simulateWithSupplier'])->name('simulation.simulate-with-supplier');

// Supplier Management Routes
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/api/suppliers', [SupplierController::class, 'list'])->name('supplier.list');
Route::get('/api/suppliers/{id}', [SupplierController::class, 'show'])->name('supplier.show');
Route::get('/api/suppliers/recommendations', [SupplierController::class, 'getRecommendations'])->name('supplier.recommendations');
Route::post('/api/suppliers/simulate', [SupplierController::class, 'simulate'])->name('supplier.simulate');

// BACKWARD COMPATIBILITY: /simulator aliases to /simulation
Route::get('/simulator', [SimulationController::class, 'index'])->name('simulator.index');
Route::post('/simulator/calculate', [SimulationController::class, 'advancedCalculate'])->name('simulator.calculate');
Route::post('/simulator/extract-story', [SimulationController::class, 'extractFromStory'])->name('simulator.extract-story');
Route::post('/simulator/chat', [SimulationController::class, 'chat'])->name('simulator.chat');
Route::get('/simulator/suppliers', [SimulationController::class, 'getSupplierRecommendations'])->name('simulator.suppliers');
Route::post('/simulator/simulate-with-supplier', [SimulationController::class, 'simulateWithSupplier'])->name('simulator.simulate-with-supplier');

Route::get('/api-settings', [ApiSettingsController::class, 'index'])->name('api-settings');

Route::get('/support', [SupportController::class, 'index'])->name('support');

// Language Switcher
Route::post('/set-locale', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'id');
    $available = config('app.available_locales', ['id', 'en']);

    if (in_array($locale, $available)) {
        $request->session()->put('locale', $locale);
    }

    return back();
})->name('set-locale');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
