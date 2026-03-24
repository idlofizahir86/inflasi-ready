<?php

use App\Http\Controllers\Api\CommoditySyncController;
use App\Http\Middleware\CheckApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/sync-price', [CommoditySyncController::class, 'store'])
    ->middleware(CheckApiKey::class);