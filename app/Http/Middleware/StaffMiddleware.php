<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra nếu người dùng đã đăng nhập và có role là 'staff'
        if (Auth::check() && Auth::user()->role === 'staff') {
            return $next($request);
        }

        // Nếu chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        // Nếu không có quyền staff
        return redirect('/')->with('error', 'Bạn không có quyền truy cập!');
    }
}

