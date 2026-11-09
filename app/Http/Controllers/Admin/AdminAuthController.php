<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan formulir login admin.
     */
    public function showLoginForm(Request $request)
    {
        if ($request->session()->get('admin_logged_in') || (! $request->session()->get('admin_logged_out') && $request->cookie('sb_admin_session') === 'active')) {
            $request->session()->put('admin_logged_in', true);

            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-login');
    }

    /**
     * Proses login admin manual dengan email & password.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email administrator wajib diisi.',
            'email.email' => 'Format email administrator tidak valid.',
            'password.required' => 'Kata sandi master wajib diisi.',
        ]);

        $email = trim(strtolower($validated['email']));
        $password = $validated['password'];

        // Kredensial resmi admin SayaBantu (default: admin@sayabantu.com / admin123)
        $validEmail = 'admin@sayabantu.com';
        $validPasswords = ['admin123', 'admin', 'sayabantu123'];

        if ($email === $validEmail && in_array($password, $validPasswords, true)) {
            $request->session()->forget('admin_logged_out');
            $request->session()->put('admin_logged_in', true);
            $request->session()->put('admin_user', [
                'name' => 'Administrator Utama',
                'email' => $email,
                'role' => 'super_admin',
                'logged_at' => now()->toIso8601String(),
            ]);
            $request->session()->regenerate();

            Cookie::queue(
                Cookie::make('sb_admin_session', 'active', 60 * 24 * 365 * 5)
            );

            $intended = $request->session()->pull('url.intended', route('admin.dashboard'));

            return redirect()->to($intended)->with('success', 'Selamat datang di Panel Kendali Admin SayaBantu!');
        }

        return back()
            ->withErrors([
                'email' => 'Kredensial tidak cocok. Gunakan email: admin@sayabantu.com dan password: admin123',
            ])
            ->withInput($request->only('email'));
    }

    /**
     * Akses cepat 1-klik untuk pengujian Administrator.
     */
    public function fastLogin(Request $request)
    {
        $request->session()->forget('admin_logged_out');
        $request->session()->put('admin_logged_in', true);
        $request->session()->put('admin_user', [
            'name' => 'Administrator Utama',
            'email' => 'admin@sayabantu.com',
            'role' => 'admin',
            'logged_at' => now()->toIso8601String(),
        ]);
        $request->session()->regenerate();

        Cookie::queue(
            Cookie::make('sb_admin_session', 'active', 60 * 24 * 365 * 5)
        );

        $intended = $request->session()->pull('url.intended', route('admin.dashboard'));

        return redirect()->to($intended)->with('success', 'Berhasil masuk cepat sebagai Administrator.');
    }

    /**
     * Keluar dari sesi admin.
     */
    public function logout(Request $request)
    {
        $request->session()->put('admin_logged_out', true);
        $request->session()->forget(['admin_logged_in', 'admin_user']);
        Cookie::queue(
            Cookie::forget('sb_admin_session')
        );
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'Anda telah berhasil keluar dari sesi admin.');
    }
}
