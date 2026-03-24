<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\File;

class SupportController extends Controller
{
    public function index()
    {
        // Membaca konten dari file yang Anda kirim
        $userManual = File::get(resource_path('markdown/USER_MANUAL.md'));
        $appOverview = File::get(resource_path('markdown/APPLICATION_OVERVIEW.md'));

        return Inertia::render('Support/Index', [
            'userManual' => $userManual,
            'appOverview' => $appOverview,
        ]);
    }
}