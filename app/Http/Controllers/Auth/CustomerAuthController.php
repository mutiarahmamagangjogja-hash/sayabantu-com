<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminRealtimeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    /**
     * Normalisasi nomor telepon ke format lokal Indonesia (08xxx).
     */
    protected function normalizePhone(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    /**
     * Pendaftaran akun baru customer dan simpan permanen ke database.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 2 karakter.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'phone.min' => 'Nomor WhatsApp minimal 9 digit.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone']);

        $existingUser = User::where('phone', $normalizedPhone)->first();
        if ($existingUser !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp ini sudah pernah terdaftar di database! Silakan langsung masuk.',
            ], 422);
        }

        $email = ! empty($validated['email'])
            ? trim(strtolower($validated['email']))
            : ($normalizedPhone.'@customer.sayabantu.com');

        $user = User::create([
            'name' => trim($validated['name']),
            'phone' => $normalizedPhone,
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'aktif',
        ]);

        Auth::login($user, true);
        $request->session()->forget('customer_logged_out');
        $request->session()->put('customer_auth', [
            'isLoggedIn' => true,
            'name' => $user->name,
            'phone' => $user->phone,
            'role' => 'customer',
        ]);
        $request->session()->put('customer_name', $user->name);
        $request->session()->put('customer_phone', $user->phone);
        $request->session()->regenerate();

        try {
            app(AdminRealtimeService::class)->recordNewCustomerRegistration($user);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran akun customer berhasil dan tersimpan di database.',
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
            'redirect' => route('customer.dashboard'),
        ]);
    }

    /**
     * Masuk ke akun customer yang telah tersimpan di database.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required' => 'Nomor WhatsApp atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $inputIdentifier = trim($validated['phone']);
        $normalizedPhone = $this->normalizePhone($inputIdentifier);

        $user = User::where(function ($query) use ($normalizedPhone, $inputIdentifier) {
            if ($normalizedPhone !== '') {
                $query->where('phone', $normalizedPhone);
            }
            $query->orWhere('email', strtolower($inputIdentifier));
        })->first();

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor WhatsApp ini belum terdaftar di database. Silakan klik "Daftar Akun Baru" terlebih dahulu.',
            ], 422);
        }

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.',
            ], 422);
        }

        Auth::login($user, true);
        $request->session()->forget('customer_logged_out');
        $request->session()->put('customer_auth', [
            'isLoggedIn' => true,
            'name' => $user->name,
            'phone' => $user->phone,
            'role' => 'customer',
        ]);
        $request->session()->put('customer_name', $user->name);
        $request->session()->put('customer_phone', $user->phone);
        $request->session()->regenerate();

        $cookie = cookie('sb_customer_logged_out', 'false', 525600);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil masuk ke akun SayaBantu!',
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
            'redirect' => route('customer.dashboard'),
        ])->withCookie($cookie);
    }

    /**
     * Keluar dari akun customer.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
        $request->session()->put('customer_logged_out', true);
        $request->session()->forget(['customer_auth', 'customer_name', 'customer_phone']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $cookie = cookie('sb_customer_logged_out', 'true', 525600);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil keluar.',
                'redirect' => route('customer.index'),
            ])->withCookie($cookie);
        }

        return redirect()->route('customer.index')->withCookie($cookie);
    }

    /**
     * Cek status sesi customer saat halaman dibuka.
     */
    public function status(Request $request): JsonResponse
    {
        $explicitLogout = $request->session()->get('customer_logged_out') === true
            || $request->cookie('sb_customer_logged_out') === 'true';

        if ($explicitLogout) {
            return response()->json([
                'isLoggedIn' => false,
            ]);
        }

        if (Auth::check()) {
            $user = Auth::user();

            return response()->json([
                'isLoggedIn' => true,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role ?? 'customer',
            ]);
        }

        $sessionCustomer = $request->session()->get('customer_auth');
        if ($sessionCustomer) {
            return response()->json($sessionCustomer);
        }

        // Auto-login active customer default jika belum pernah klik logout
        $defaultCustomer = User::where('role', 'customer')->first();
        if ($defaultCustomer) {
            Auth::login($defaultCustomer, true);
            $sessionData = [
                'isLoggedIn' => true,
                'name' => $defaultCustomer->name,
                'phone' => $defaultCustomer->phone,
                'role' => 'customer',
            ];
            $request->session()->put('customer_auth', $sessionData);

            return response()->json($sessionData);
        }

        return response()->json([
            'isLoggedIn' => false,
        ]);
    }
}
