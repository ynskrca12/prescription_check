<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PhysicianAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('physician')->check()) {
            return redirect()->route('physician.login')
                ->with('error', 'Lütfen giriş yapınız.');
        }

        if (!auth('physician')->user()->is_active) {
            auth('physician')->logout();
            return redirect()->route('physician.login')
                ->with('error', 'Hesabınız aktif değil. Lütfen yönetici ile iletişime geçin.');
        }

        return $next($request);
    }
}
