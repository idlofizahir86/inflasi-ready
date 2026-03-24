<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoLoginDemo
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user belum login dan akses login page
        if (!Auth::check() && $request->path() === 'login') {
            // Auto-fill dengan demo credentials
            return response()->view('auth.login', [
                'demo_email' => 'akun-demo@pidi.id',
                'demo_password' => '123456',
            ]);
        }

        // Jika user belum login dan akses route yang butuh auth, redirect ke login
        if (!Auth::check() && $request->path() !== 'login' && $request->path() !== 'register') {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
