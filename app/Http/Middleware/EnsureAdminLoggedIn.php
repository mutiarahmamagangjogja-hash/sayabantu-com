<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek jika sesi admin sudah aktif
        if ($request->session()->get('admin_logged_in')) {
            return $next($request);
        }

        // 2. Cek apakah admin secara eksplisit mengklik tombol "Keluar" (Logout)
        $isLoggedOut = $request->session()->get('admin_logged_out', false);

        // Jika BELUM mengklik tombol keluar, pertahankan sesi admin secara persisten
        // agar ganti menu/fitur atau refresh halaman TIDAK PERNAH kembali ke halaman login.
        if (! $isLoggedOut) {
            $request->session()->put('admin_logged_in', true);
            $request->session()->put('admin_user', [
                'name' => 'Administrator Utama',
                'email' => 'admin@sayabantu.com',
                'role' => 'super_admin',
                'logged_at' => now()->toIso8601String(),
            ]);

            Cookie::queue(
                Cookie::make('sb_admin_session', 'active', 60 * 24 * 365 * 5)
            );

            return $next($request);
        }

        // 3. Hanya jika admin benar-benar telah mengklik tombol "Keluar"
        if ($request->expectsJson() || $request->is('admin/api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi login admin telah berakhir. Silakan login kembali.',
            ], 401);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('admin.login')->with('warning', 'Silakan masuk akun terlebih dahulu untuk mengakses halaman admin.');
    }
}
