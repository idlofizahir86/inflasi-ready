<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ApiSettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('ApiSettings', [
        ]);
    }
}
