<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Services\AdminRealtimeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MitraAuthController extends Controller
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
        } elseif (str_starts_with($digits, '8')) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    /**
     * Generate ID Mitra unik (contoh: SB-8801).
     */
    protected function generateMitraId(): string
    {
        do {
            $id = 'SB-'.random_int(1000, 9999);
        } while (Mitra::where('mitra_id', $id)->exists());

        return $id;
    }

    /**
     * Pendaftaran mitra baru dan simpan permanen ke database dengan status verifikasi.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'min:2', 'max:255'],
            'nik' => ['nullable', 'string', 'min:10', 'max:30'],
            'whatsapp' => ['required', 'string', 'min:9', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'kota' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'pin' => ['required', 'string', 'min:6'],
            'keahlian' => ['nullable', 'string'],
            'service' => ['nullable', 'string'],
            'jaminan' => ['nullable', 'string'],
            'selfie' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_wajah' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'pin.required' => 'PIN / kata sandi wajib diisi.',
            'pin.min' => 'PIN / kata sandi minimal 6 karakter.',
            'kota.required' => 'Kota penugasan wajib dipilih.',
            'selfie.required' => 'Foto selfie sambil memegang e-KTP wajib diunggah.',
            'selfie.image' => 'Berkas selfie e-KTP harus berupa gambar.',
            'selfie.mimes' => 'Foto selfie e-KTP harus berformat JPG, PNG, atau WebP.',
            'selfie.max' => 'Ukuran foto selfie e-KTP maksimal 5 MB.',
            'foto_wajah.required' => 'Foto wajah / foto diri wajib diunggah.',
            'foto_wajah.image' => 'Berkas foto wajah harus berupa gambar.',
            'foto_wajah.mimes' => 'Foto wajah harus berformat JPG, PNG, atau WebP.',
            'foto_wajah.max' => 'Ukuran foto wajah maksimal 5 MB.',
        ]);

        $normalizedPhone = $this->normalizePhone($validated['whatsapp']);

        // Cek apakah nomor WhatsApp sudah terdaftar
        $existing = Mitra::where('phone', $normalizedPhone)->first();

        $mitraId = $this->generateMitraId();
        $avatarPath = $request->file('foto_wajah')->store('mitra/avatars', 'public');
        $avatarUrl = Storage::disk('public')->url($avatarPath);

        if ($existing !== null) {
            $existing->update([
                'name' => trim($validated['nama']),
                'nik' => $validated['nik'] ?? $existing->nik,
                'email' => ! empty($validated['email']) ? trim(strtolower($validated['email'])) : $existing->email,
                'kota' => $validated['kota'],
                'area' => $validated['area'] ?? $validated['kota'],
                'alamat' => $validated['alamat'] ?? $existing->alamat,
                'pin' => Hash::make($validated['pin']),
                'keahlian' => $validated['keahlian'] ?? $existing->keahlian,
                'service' => $validated['service'] ?? ($validated['keahlian'] ?? $existing->service),
                'jaminan' => $validated['jaminan'] ?? $existing->jaminan,
                'avatar' => $avatarUrl,
                'status' => 'verifikasi',
                'status_label' => 'Menunggu Verifikasi & ACC Admin',
                'rejection_reason' => null,
            ]);
            $mitra = $existing;
        } else {
            $mitra = Mitra::create([
                'mitra_id' => $mitraId,
                'name' => trim($validated['nama']),
                'nik' => $validated['nik'] ?? null,
                'phone' => $normalizedPhone,
                'email' => ! empty($validated['email']) ? trim(strtolower($validated['email'])) : null,
                'kota' => $validated['kota'],
                'area' => $validated['area'] ?? $validated['kota'],
                'alamat' => $validated['alamat'] ?? null,
                'pin' => Hash::make($validated['pin']),
                'keahlian' => $validated['keahlian'] ?? null,
                'service' => $validated['service'] ?? ($validated['keahlian'] ?? null),
                'jaminan' => $validated['jaminan'] ?? 'KTP Asli',
                'avatar' => $avatarUrl,
                'status' => 'verifikasi',
                'status_label' => 'Menunggu Verifikasi & ACC Admin',
            ]);
        }

        if ($request->hasSession()) {
            $request->session()->forget('mitra_logged_out');
            $request->session()->put('mitra_auth', [
                'isLoggedIn' => false,
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'name' => $mitra->name,
                'phone' => $mitra->phone,
                'role' => 'mitra',
                'status' => 'verifikasi',
                'specialty' => $mitra->keahlian,
            ]);
        }

        try {
            app(AdminRealtimeService::class)->recordNewMitraRegistration($mitra);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil dikirim. Status akun Anda saat ini sedang ditinjau oleh Admin.',
            'status' => 'pending_approval',
            'redirect' => route('mitra.pending'),
            'mitra' => [
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'regNo' => $mitra->mitra_id,
                'name' => $mitra->name,
                'nama' => $mitra->name,
                'phone' => $mitra->phone,
                'whatsapp' => $mitra->phone,
                'kota' => $mitra->kota,
                'area' => $mitra->area,
                'keahlian' => $mitra->keahlian,
                'service' => $mitra->service,
                'avatar' => $mitra->avatar,
                'rating' => null,
                'tasks' => 0,
                'status' => $mitra->status,
                'statusLabel' => $mitra->status_label,
            ],
        ])->withCookie(cookie('sb_mitra_logged_out', 'false', 525600));
    }

    /**
     * Masuk ke akun mitra menggunakan ID Mitra, Nomor WhatsApp, atau NIK dan PIN.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'pin' => ['required', 'string'],
        ], [
            'identifier.required' => 'ID Mitra, Nomor WhatsApp, atau Nama wajib diisi.',
            'pin.required' => 'PIN keamanan / kata sandi wajib diisi.',
        ]);

        $input = trim($validated['identifier']);
        $normalizedPhone = $this->normalizePhone($input);
        $cleanDigits = preg_replace('/[^0-9]/', '', $input);
        $cleanAlphaNum = preg_replace('/[^a-zA-Z0-9]/', '', $input);
        $cleanId = strtoupper(str_replace(['#', ' ', '-'], '', $input));

        $mitra = Mitra::where(function ($query) use ($input, $normalizedPhone, $cleanDigits, $cleanAlphaNum, $cleanId) {
            if ($normalizedPhone !== '') {
                $query->where('phone', $normalizedPhone);
            }
            if ($cleanDigits !== '' && strlen($cleanDigits) >= 6) {
                $query->orWhere('phone', $cleanDigits)
                    ->orWhere('phone', '0'.ltrim($cleanDigits, '0'));
                if (strlen($cleanDigits) >= 8) {
                    $query->orWhere('phone', 'like', '%'.substr($cleanDigits, -8));
                }
            }
            if ($input !== '') {
                $query->orWhere('mitra_id', $input)
                    ->orWhere('mitra_id', 'SB-'.ltrim(strtoupper($cleanId), 'SB'))
                    ->orWhere('mitra_id', '#'.$cleanAlphaNum)
                    ->orWhere('mitra_id', 'like', '%'.$cleanAlphaNum.'%');
            }
            if ($cleanAlphaNum !== '') {
                $query->orWhere('nik', $cleanAlphaNum);
            }
            $query->orWhere('email', strtolower($input));
            $query->orWhereRaw('LOWER(name) = ?', [strtolower($input)])
                ->orWhere('name', 'like', '%'.$input.'%');
        })->first();

        if ($mitra === null) {
            return response()->json([
                'success' => false,
                'message' => 'Akun mitra dengan data ini belum terdaftar di database. Silakan daftar terlebih dahulu melalui formulir pendaftaran mitra.',
            ], 422);
        }

        // Verifikasi PIN (support bcrypt Hash, plain string, dan password dari User jika ada)
        $pinMatch = Hash::check($validated['pin'], $mitra->pin) || ($mitra->pin === $validated['pin']);
        if (! $pinMatch && $mitra->phone) {
            $linkedUser = \App\Models\User::where('phone', $mitra->phone)->first();
            if ($linkedUser && Hash::check($validated['pin'], $linkedUser->password)) {
                $pinMatch = true;
            }
        }

        if (! $pinMatch) {
            return response()->json([
                'success' => false,
                'message' => 'PIN keamanan yang Anda masukkan salah. Silakan coba lagi.',
            ], 422);
        }

        // Cek status akun mitra: pending_approval, approved, atau rejected
        if ($mitra->status === 'pending_approval' || $mitra->status === 'verifikasi') {
            $request->session()->put('mitra_auth', [
                'isLoggedIn' => false,
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'name' => $mitra->name,
                'phone' => $mitra->phone,
                'role' => 'mitra',
                'status' => 'pending_approval',
                'specialty' => $mitra->keahlian,
            ]);

            return response()->json([
                'success' => false,
                'status' => 'pending_approval',
                'heading' => 'Pendaftaran Sedang Ditinjau Admin',
                'message' => 'Proses verifikasi membutuhkan waktu maksimal 1x24 jam kerja.',
                'redirect' => route('mitra.pending'),
            ], 403);
        }

        if ($mitra->status === 'rejected' || $mitra->status === 'ditolak') {
            $reason = $mitra->rejection_reason ?: 'Berkas pendaftaran atau data persyaratan belum memenuhi standar platform.';
            $canReapply = (bool) $mitra->can_reapply;

            $request->session()->put('mitra_auth', [
                'isLoggedIn' => false,
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'name' => $mitra->name,
                'phone' => $mitra->phone,
                'role' => 'mitra',
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'can_reapply' => $canReapply,
                'specialty' => $mitra->keahlian,
            ]);

            return response()->json([
                'success' => false,
                'status' => 'rejected',
                'heading' => 'Pendaftaran Belum Dapat Disetujui',
                'reason' => $reason,
                'can_reapply' => $canReapply,
                'message' => 'Pendaftaran akun mitra Anda belum dapat disetujui oleh admin.',
                'redirect' => route('mitra.pending'),
            ], 403);
        }

        if ($mitra->status === 'suspend') {
            return response()->json([
                'success' => false,
                'status' => 'suspend',
                'message' => 'Akun mitra Anda saat ini sedang ditangguhkan/suspend oleh administrator.',
            ], 403);
        }

        // Status approved / aktif -> login berhasil penuh
        $request->session()->forget('mitra_logged_out');
        $request->session()->put('mitra_auth', [
            'isLoggedIn' => true,
            'id' => $mitra->id,
            'mitra_id' => $mitra->mitra_id,
            'name' => $mitra->name,
            'phone' => $mitra->phone,
            'role' => 'mitra',
            'status' => 'approved',
            'specialty' => $mitra->keahlian,
        ]);
        $request->session()->regenerate();

        $cookie = cookie('sb_mitra_logged_out', 'false', 525600);

        return response()->json([
            'success' => true,
            'status' => 'approved',
            'message' => 'Pendaftaran Diterima! Selamat bertugas di Ruang Kerja Mitra.',
            'mitra' => [
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'name' => $mitra->name,
                'phone' => $mitra->phone,
                'status' => 'approved',
                'specialty' => $mitra->keahlian,
            ],
            'redirect' => route('mitra.dashboard'),
        ])->withCookie($cookie);
    }

    /**
     * Mengambil daftar mitra untuk panel admin.
     */
    public function getAdminMitraList(): JsonResponse
    {
        $mitras = Mitra::query()
            ->withCount(['completedOrders', 'reviewedOrders'])
            ->withAvg('reviewedOrders', 'review_rating')
            ->orderByDesc('id')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->mitra_id ?: 'SB-'.str_pad((string) $m->id, 4, '0', STR_PAD_LEFT),
                    'db_id' => $m->id,
                    'regNo' => $m->mitra_id ?: 'SB-'.str_pad((string) $m->id, 4, '0', STR_PAD_LEFT),
                    'nama' => $m->name,
                    'nik' => $m->nik,
                    'whatsapp' => $m->phone,
                    'phone' => $m->phone,
                    'email' => $m->email,
                    'kota' => $m->kota,
                    'area' => $m->area,
                    'alamat' => $m->alamat,
                    'keahlian' => $m->keahlian,
                    'service' => $m->service ?: $m->keahlian,
                    'jaminan' => $m->jaminan,
                    'avatar' => $m->avatar,
                    'image' => $m->avatar,
                    'rating' => $m->reviewed_orders_avg_review_rating === null
                        ? null
                        : number_format((float) $m->reviewed_orders_avg_review_rating, 1),
                    'reviewCount' => $m->reviewed_orders_count,
                    'tasks' => $m->completed_orders_count,
                    'status' => $m->status,
                    'statusLabel' => $m->status_label,
                    'registeredAt' => $m->created_at ? $m->created_at->toISOString() : null,
                ];
            });

        return response()->json([
            'success' => true,
            'mitras' => $mitras,
        ]);
    }

    /**
     * Verifikasi & ACC Mitra oleh Admin di database.
     */
    public function accMitra(Request $request, int|string $id): JsonResponse
    {
        $idClean = trim((string) $id);
        $normPhone = $this->normalizePhone($idClean);
        $cleanId = strtoupper(str_replace(['#', ' '], '', $idClean));

        $mitra = null;
        if (is_numeric($idClean)) {
            $mitra = Mitra::find((int) $idClean);
        }

        if ($mitra === null) {
            $mitra = Mitra::where('mitra_id', $idClean)
                ->orWhere('mitra_id', $cleanId)
                ->orWhere('mitra_id', 'SB-'.$cleanId)
                ->orWhere('mitra_id', '#'.$cleanId)
                ->when($normPhone !== '', fn ($q) => $q->orWhere('phone', $normPhone))
                ->first();
        }

        if ($mitra === null) {
            return response()->json([
                'success' => false,
                'message' => 'Mitra tidak ditemukan di database.',
            ], 404);
        }

        $mitra->update([
            'status' => 'approved',
            'status_label' => 'Disetujui & Aktif',
            'rejection_reason' => null,
        ]);

        if ($request->hasSession()) {
            $session = $request->session()->get('mitra_auth');
            if ($session && (($session['mitra_id'] ?? null) === $mitra->mitra_id || ($session['phone'] ?? null) === $mitra->phone)) {
                $session['status'] = 'approved';
                $session['isLoggedIn'] = true;
                $request->session()->put('mitra_auth', $session);
            }
        }

        try {
            app(AdminRealtimeService::class)->recordMitraApproved($mitra);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Mitra {$mitra->name} ({$mitra->mitra_id}) berhasil disetujui (Approved) & di-ACC oleh Admin!",
            'mitra' => $mitra,
        ]);
    }

    /**
     * Tolak pendaftaran mitra di database dengan alasan dan opsi izinkan kirim ulang (Skenario A).
     */
    public function tolakMitra(Request $request, int|string $id): JsonResponse
    {
        $idClean = trim((string) $id);
        $normPhone = $this->normalizePhone($idClean);
        $cleanId = strtoupper(str_replace(['#', ' '], '', $idClean));

        $mitra = null;
        if (is_numeric($idClean)) {
            $mitra = Mitra::find((int) $idClean);
        }

        if ($mitra === null) {
            $mitra = Mitra::where('mitra_id', $idClean)
                ->orWhere('mitra_id', $cleanId)
                ->orWhere('mitra_id', 'SB-'.$cleanId)
                ->orWhere('mitra_id', '#'.$cleanId)
                ->when($normPhone !== '', fn ($q) => $q->orWhere('phone', $normPhone))
                ->first();
        }

        if ($mitra === null) {
            return response()->json([
                'success' => false,
                'message' => 'Mitra tidak ditemukan di database.',
            ], 404);
        }

        $reason = trim((string) $request->input('reason')) ?: 'Berkas identitas atau persyaratan belum memenuhi ketentuan verifikasi.';
        $canReapply = $request->boolean('can_reapply', false);

        $mitra->update([
            'status' => 'rejected',
            'status_label' => 'Pendaftaran Belum Dapat Disetujui',
            'rejection_reason' => $reason,
            'can_reapply' => $canReapply,
        ]);

        if ($request->hasSession()) {
            $session = $request->session()->get('mitra_auth');
            if ($session && (($session['mitra_id'] ?? null) === $mitra->mitra_id || ($session['phone'] ?? null) === $mitra->phone)) {
                $session['status'] = 'rejected';
                $session['rejection_reason'] = $reason;
                $session['can_reapply'] = $canReapply;
                $session['isLoggedIn'] = false;
                $request->session()->put('mitra_auth', $session);
            }
        }

        try {
            app(AdminRealtimeService::class)->recordMitraRejected($mitra, $reason, $canReapply);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "Pendaftaran mitra {$mitra->name} telah ditolak.",
            'reason' => $reason,
            'can_reapply' => $canReapply,
            'mitra' => $mitra,
        ]);
    }

    /**
     * Layar penantian verifikasi (Pending Screen) & Penolakan (Rejection Screen) untuk mitra.
     */
    public function pending(Request $request): View|RedirectResponse
    {
        $session = $request->session()->get('mitra_auth');
        $phone = $session['phone'] ?? null;
        $mitraId = $session['mitra_id'] ?? null;

        $mitra = null;
        if ($phone || $mitraId) {
            $mitra = Mitra::where(function ($q) use ($phone, $mitraId) {
                if ($phone) {
                    $q->where('phone', $phone);
                }
                if ($mitraId) {
                    $q->orWhere('mitra_id', $mitraId);
                }
            })->first();
        }

        $status = $mitra?->status ?? ($session['status'] ?? 'pending_approval');

        // Jika sudah disetujui / aktif, langsung arahkan ke Dashboard Mitra tanpa login ulang
        if ($status === 'approved' || $status === 'aktif') {
            if ($mitra && $request->hasSession()) {
                $session = [
                    'isLoggedIn' => true,
                    'id' => $mitra->id,
                    'mitra_id' => $mitra->mitra_id,
                    'name' => $mitra->name,
                    'phone' => $mitra->phone,
                    'role' => 'mitra',
                    'status' => 'approved',
                    'specialty' => $mitra->keahlian,
                ];
                $request->session()->put('mitra_auth', $session);
                $request->session()->forget('mitra_logged_out');
            }

            return redirect()->route('mitra.dashboard')->withCookie(cookie('sb_mitra_logged_out', 'false', 525600));
        }

        $reason = $mitra?->rejection_reason ?? ($session['rejection_reason'] ?? 'Berkas pendaftaran atau data persyaratan belum memenuhi standar platform.');
        $canReapply = $mitra?->can_reapply ?? ($session['can_reapply'] ?? false);

        return view('mitra.pending', [
            'mitra' => $mitra,
            'status' => $status,
            'reason' => $reason,
            'canReapply' => $canReapply,
        ]);
    }

    /**
     * Suspend akun mitra di database.
     */
    public function suspendMitra(Request $request, int|string $id): JsonResponse
    {
        $idClean = trim((string) $id);
        $normPhone = $this->normalizePhone($idClean);
        $cleanId = strtoupper(str_replace(['#', ' '], '', $idClean));

        $mitra = null;
        if (is_numeric($idClean)) {
            $mitra = Mitra::find((int) $idClean);
        }

        if ($mitra === null) {
            $mitra = Mitra::where('mitra_id', $idClean)
                ->orWhere('mitra_id', $cleanId)
                ->orWhere('mitra_id', 'SB-'.$cleanId)
                ->orWhere('mitra_id', '#'.$cleanId)
                ->when($normPhone !== '', fn ($q) => $q->orWhere('phone', $normPhone))
                ->first();
        }

        if ($mitra === null) {
            return response()->json([
                'success' => false,
                'message' => 'Mitra tidak ditemukan di database.',
            ], 404);
        }

        $mitra->update([
            'status' => 'suspend',
            'status_label' => 'Ditangguhkan',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Akun mitra {$mitra->name} telah ditangguhkan.",
            'mitra' => $mitra,
        ]);
    }

    /**
     * Ambil data mitra yang terverifikasi dan aktif untuk halaman Mitra Per Kota publik / customer.
     */
    public function getMitraPerKota(Request $request): JsonResponse
    {
        $query = Mitra::query()
            ->whereIn('status', ['aktif', 'approved', 'disetujui'])
            ->withCount(['completedOrders', 'reviewedOrders'])
            ->withAvg('reviewedOrders', 'review_rating');

        $kota = trim($request->string('kota')->value());
        if ($kota !== '') {
            $query->whereRaw('LOWER(kota) = ?', [mb_strtolower($kota)]);
        }

        $mitras = $query
            ->orderBy('kota')
            ->orderBy('name')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->mitra_id ?: 'SB-'.str_pad((string) $m->id, 4, '0', STR_PAD_LEFT),
                    'db_id' => $m->id,
                    'regNo' => $m->mitra_id ?: 'SB-'.str_pad((string) $m->id, 4, '0', STR_PAD_LEFT),
                    'nama' => $m->name,
                    'whatsapp' => $m->phone,
                    'phone' => $m->phone,
                    'kota' => $m->kota,
                    'area' => $m->area,
                    'keahlian' => $m->keahlian,
                    'service' => $m->service ?: $m->keahlian,
                    'jaminan' => $m->jaminan,
                    'avatar' => $m->avatar,
                    'image' => $m->avatar,
                    'rating' => $m->reviewed_orders_avg_review_rating === null
                        ? null
                        : number_format((float) $m->reviewed_orders_avg_review_rating, 1),
                    'reviewCount' => $m->reviewed_orders_count,
                    'tasks' => $m->completed_orders_count,
                    'status' => $m->status,
                    'statusLabel' => $m->status_label,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $mitras->count(),
            'mitras' => $mitras,
        ]);
    }

    /**
     * Keluar dari sesi mitra.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $request->session()->put('mitra_logged_out', true);
        $request->session()->forget('mitra_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $cookie = cookie('sb_mitra_logged_out', 'true', 525600);

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
     * Cek status aktif mitra secara langsung dari database SQLite.
     */
    public function status(Request $request): JsonResponse
    {
        $session = $request->session()->get('mitra_auth');
        $identifier = $request->input('identifier') ?? ($session['phone'] ?? ($session['mitra_id'] ?? null));

        if ($identifier) {
            $input = trim((string) $identifier);
            $norm = $this->normalizePhone($input);
            $cleanDigits = preg_replace('/[^0-9]/', '', $input);
            $cleanAlphaNum = preg_replace('/[^a-zA-Z0-9]/', '', $input);
            $cleanId = strtoupper(str_replace(['#', ' ', '-'], '', $input));

            $mitra = null;
            if (is_numeric($input)) {
                $mitra = Mitra::find((int) $input);
            }
            if ($mitra === null) {
                $mitra = Mitra::where(function ($query) use ($input, $norm, $cleanDigits, $cleanAlphaNum, $cleanId) {
                    if ($norm !== '') {
                        $query->where('phone', $norm);
                    }
                    if ($cleanDigits !== '' && strlen($cleanDigits) >= 6) {
                        $query->orWhere('phone', $cleanDigits)
                            ->orWhere('phone', '0'.ltrim($cleanDigits, '0'));
                        if (strlen($cleanDigits) >= 8) {
                            $query->orWhere('phone', 'like', '%'.substr($cleanDigits, -8));
                        }
                    }
                    if ($input !== '') {
                        $query->orWhere('mitra_id', $input)
                            ->orWhere('mitra_id', 'SB-'.ltrim(strtoupper($cleanId), 'SB'))
                            ->orWhere('mitra_id', '#'.$cleanAlphaNum)
                            ->orWhere('mitra_id', 'like', '%'.$cleanAlphaNum.'%');
                    }
                    if ($cleanAlphaNum !== '') {
                        $query->orWhere('nik', $cleanAlphaNum);
                    }
                    $query->orWhere('email', strtolower($input));
                    $query->orWhereRaw('LOWER(name) = ?', [strtolower($input)])
                        ->orWhere('name', 'like', '%'.$input.'%');
                })->first();
            }

            if ($mitra) {
                if (in_array($mitra->status, ['aktif', 'approved'], true)) {
                    $authSession = [
                        'isLoggedIn' => true,
                        'id' => $mitra->id,
                        'mitra_id' => $mitra->mitra_id,
                        'name' => $mitra->name,
                        'phone' => $mitra->phone,
                        'role' => 'mitra',
                        'status' => 'approved',
                        'specialty' => $mitra->keahlian,
                    ];
                    $request->session()->put('mitra_auth', $authSession);
                    $request->session()->forget('mitra_logged_out');

                    return response()->json([
                        'isLoggedIn' => true,
                        'status' => 'approved',
                        'status_label' => $mitra->status_label ?: 'Disetujui & Aktif',
                        'id' => $mitra->id,
                        'mitra_id' => $mitra->mitra_id,
                        'name' => $mitra->name,
                        'phone' => $mitra->phone,
                        'role' => 'mitra',
                        'redirect' => route('mitra.dashboard'),
                    ])->withCookie(cookie('sb_mitra_logged_out', 'false', 525600));
                }

                if (in_array($mitra->status, ['rejected', 'ditolak'], true)) {
                    $request->session()->put('mitra_auth', [
                        'isLoggedIn' => false,
                        'id' => $mitra->id,
                        'mitra_id' => $mitra->mitra_id,
                        'name' => $mitra->name,
                        'phone' => $mitra->phone,
                        'role' => 'mitra',
                        'status' => 'rejected',
                        'rejection_reason' => $mitra->rejection_reason,
                        'can_reapply' => (bool) $mitra->can_reapply,
                    ]);

                    return response()->json([
                        'isLoggedIn' => false,
                        'status' => 'rejected',
                        'status_label' => $mitra->status_label ?: 'Ditolak',
                        'id' => $mitra->id,
                        'mitra_id' => $mitra->mitra_id,
                        'name' => $mitra->name,
                        'phone' => $mitra->phone,
                        'role' => 'mitra',
                        'rejection_reason' => $mitra->rejection_reason,
                        'can_reapply' => (bool) $mitra->can_reapply,
                        'redirect' => route('mitra.pending'),
                    ]);
                }

                return response()->json([
                    'isLoggedIn' => false,
                    'status' => 'pending_approval',
                    'status_label' => $mitra->status_label ?: 'Menunggu Verifikasi & ACC Admin',
                    'id' => $mitra->id,
                    'mitra_id' => $mitra->mitra_id,
                    'name' => $mitra->name,
                    'phone' => $mitra->phone,
                    'role' => 'mitra',
                    'redirect' => null,
                ]);
            }
        }

        $explicitLogout = $request->session()->get('mitra_logged_out') === true
            || $request->cookie('sb_mitra_logged_out') === 'true';

        if ($explicitLogout) {
            return response()->json([
                'isLoggedIn' => false,
                'role' => 'mitra',
                'status' => null,
            ]);
        }

        if ($session) {
            return response()->json($session);
        }

        // Auto restore active mitra default jika belum pernah logout
        $defaultMitra = Mitra::whereIn('status', ['approved', 'aktif'])->latest('updated_at')->first();
        if ($defaultMitra) {
            $session = [
                'isLoggedIn' => true,
                'id' => $defaultMitra->id,
                'mitra_id' => $defaultMitra->mitra_id,
                'name' => $defaultMitra->name,
                'phone' => $defaultMitra->phone,
                'role' => 'mitra',
                'status' => $defaultMitra->status,
                'specialty' => $defaultMitra->keahlian,
            ];
            $request->session()->put('mitra_auth', $session);

            return response()->json($session);
        }

        return response()->json([
            'isLoggedIn' => false,
            'role' => 'mitra',
            'status' => null,
        ]);
    }
}
