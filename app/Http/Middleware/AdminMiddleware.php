<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Debug log
        \Log::info('AdminMiddleware - Checking access', [
            'url' => $request->url(),
            'is_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user_role' => Auth::check() ? Auth::user()->role : null,
        ]);

        // Kiểm tra nếu người dùng đã đăng nhập và có role là 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            \Log::info('AdminMiddleware - Access GRANTED');
            return $next($request);
        }

        // Nếu chưa đăng nhập
        if (!Auth::check()) {
            \Log::warning('AdminMiddleware - User NOT authenticated, redirecting to login');
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        // Nếu không có quyền admin
        \Log::warning('AdminMiddleware - User role is NOT admin', [
            'user_id' => Auth::id(),
            'role' => Auth::user()->role,
        ]);
        return redirect('/')->with('error', 'Bạn không có quyền truy cập!');
    }
}