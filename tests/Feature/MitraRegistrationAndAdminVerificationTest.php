<?php

namespace Tests\Feature;

use App\Models\Mitra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MitraRegistrationAndAdminVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_registration_stores_in_database_with_verifikasi_status(): void
    {
        Storage::fake('public');

        $payload = [
            'nama' => 'Kala Mitra Penguji',
            'nik' => '3174987654321098',
            'whatsapp' => '088899988776',
            'email' => 'kala.test@sayabantu.com',
            'kota' => 'Jakarta',
            'area' => 'Jakarta (Kebayoran Baru)',
            'alamat' => 'Jl. Kebayoran Baru No. 12',
            'pin' => '123456',
            'keahlian' => 'Bersih-bersih & Housekeeping',
            'service' => 'Bersih-bersih & Housekeeping',
            'jaminan' => 'BPKB Sepeda Motor Asli (#BPKB-JKT-12345)',
            'selfie' => UploadedFile::fake()->image('wajah.jpg'),
            'foto_wajah' => UploadedFile::fake()->image('foto-diri.jpg'),
        ];

        $response = $this->postJson(route('mitra.daftar.submit'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'pending_approval',
            ]);

        $this->assertDatabaseHas('mitras', [
            'name' => 'Kala Mitra Penguji',
            'nik' => '3174987654321098',
            'phone' => '088899988776',
            'status' => 'verifikasi',
        ]);
        $this->assertCount(1, Storage::disk('public')->allFiles('mitra/avatars'));
        $avatarUrl = Mitra::where('phone', '088899988776')->value('avatar');
        $this->assertStringContainsString('/storage/mitra/avatars/', $avatarUrl);
        $this->assertTrue(Storage::disk('public')->exists(str_replace('/storage/', '', parse_url($avatarUrl, PHP_URL_PATH))));
    }

    public function test_mitra_registration_updates_existing_mitra_to_verifikasi(): void
    {
        Storage::fake('public');

        Mitra::create([
            'mitra_id' => 'SB-7615',
            'name' => 'Kala Penguji',
            'nik' => '3174091507920003',
            'phone' => '081234567890',
            'kota' => 'Jakarta',
            'pin' => bcrypt('123456'),
            'status' => 'approved',
        ]);

        $payload = [
            'nama' => 'Kala Penguji',
            'nik' => '3174091507920003',
            'whatsapp' => '0812-3456-7890',
            'kota' => 'Jakarta',
            'pin' => '123456',
            'selfie' => UploadedFile::fake()->image('wajah.jpg'),
            'foto_wajah' => UploadedFile::fake()->image('foto-diri.jpg'),
        ];

        $response = $this->postJson(route('mitra.daftar.submit'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'pending_approval',
            ]);

        $this->assertDatabaseHas('mitras', [
            'name' => 'Kala Penguji',
            'phone' => '081234567890',
            'status' => 'verifikasi',
        ]);
    }

    public function test_registered_mitra_appears_in_admin_portal_and_counted_in_verifikasi(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'MTR-7721',
            'name' => 'Ahmad Baru Terdaftar',
            'nik' => '3174112233445566',
            'phone' => '089912345678',
            'email' => 'ahmad.baru@sayabantu.com',
            'kota' => 'Jakarta',
            'pin' => bcrypt('123456'),
            'status' => 'verifikasi',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
            'jaminan' => 'BPKB Sepeda Motor Asli',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get('/admin/pengguna?tab=mitra');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Baru Terdaftar');
        $response->assertSee('MTR-7721');
        $response->assertSee('Menunggu Verifikasi &amp; ACC', false);

        $this->assertEquals('verifikasi', $mitra->fresh()->status);
    }

    public function test_admin_can_approve_and_reject_registered_mitra(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'MTR-8832',
            'name' => 'Siti Calon Mitra',
            'phone' => '087788990011',
            'kota' => 'Jakarta',
            'pin' => bcrypt('123456'),
            'status' => 'verifikasi',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
        ]);

        // ACC / Setujui Mitra
        $accResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson("/admin/api/mitras/{$mitra->id}/acc");
        $accResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mitra.status', 'approved');

        $this->assertEquals('approved', $mitra->fresh()->status);

        // Tolak Mitra
        $tolakResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson("/admin/api/mitras/{$mitra->id}/tolak", [
                'reason' => 'Foto KTP tidak terbaca jelas',
                'can_reapply' => true,
            ]);
        $tolakResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('mitra.status', 'rejected');

        $this->assertEquals('rejected', $mitra->fresh()->status);
    }

    public function test_approved_mitra_is_automatically_authenticated_and_directed_to_dashboard_without_login(): void
    {
        Storage::fake('public');

        // 1. Mitra mendaftar
        $regPayload = [
            'nama' => 'Budi Otomatis',
            'nik' => '3171234567890001',
            'whatsapp' => '081299990000',
            'kota' => 'Jakarta',
            'pin' => '654321',
            'keahlian' => 'Full Home Cleaning',
            'selfie' => UploadedFile::fake()->image('wajah.jpg'),
            'foto_wajah' => UploadedFile::fake()->image('foto-diri.jpg'),
        ];

        $regResponse = $this->postJson(route('mitra.daftar.submit'), $regPayload);
        $regResponse->assertStatus(200);

        $mitra = Mitra::where('phone', '081299990000')->first();
        $this->assertNotNull($mitra);
        $this->assertEquals('verifikasi', $mitra->status);

        // 2. Sebelum di-ACC admin, status check mengembalikan pending_approval dan isLoggedIn: false
        $statusBefore = $this->getJson(route('mitra.status', ['identifier' => $mitra->mitra_id]));
        $statusBefore->assertStatus(200)
            ->assertJson([
                'isLoggedIn' => false,
                'status' => 'pending_approval',
            ]);

        // 3. Admin melakukan ACC / persetujuan mitra
        $accResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson("/admin/api/mitras/{$mitra->id}/acc");
        $accResponse->assertStatus(200);
        $this->assertEquals('approved', $mitra->fresh()->status);

        // 4. Polling status check setelah di-ACC otomatis login & mengembalikan redirect ke dashboard
        $statusAfter = $this->getJson(route('mitra.status', ['identifier' => $mitra->mitra_id]));
        $statusAfter->assertStatus(200)
            ->assertJson([
                'isLoggedIn' => true,
                'status' => 'approved',
                'name' => 'Budi Otomatis',
                'redirect' => route('mitra.dashboard'),
            ]);

        // 5. Akses /mitra/dashboard langsung menyajikan dashboard tanpa mental ke halaman login
        $dashboardResponse = $this->get(route('mitra.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Budi Otomatis');

        // 6. Akses /mitra/pending saat akun sudah disetujui langsung redirect ke /mitra/dashboard
        $pendingResponse = $this->get(route('mitra.pending'));
        $pendingResponse->assertRedirect(route('mitra.dashboard'));
    }

    public function test_mitra_registration_rejects_missing_selfie_without_creating_a_record(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('mitra.daftar.submit'), [
            'nama' => 'Mitra Tanpa Foto',
            'whatsapp' => '081234567801',
            'kota' => 'Depok',
            'pin' => '123456',
            'foto_wajah' => UploadedFile::fake()->image('foto-diri.jpg'),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['selfie']);
        $this->assertDatabaseMissing('mitras', ['phone' => '081234567801']);
        $this->assertSame([], Storage::disk('public')->allFiles('mitra/avatars'));
    }

    public function test_mitra_registration_rejects_missing_face_photo_without_creating_a_record(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('mitra.daftar.submit'), [
            'nama' => 'Mitra Tanpa Foto Diri',
            'whatsapp' => '081234567802',
            'kota' => 'Depok',
            'pin' => '123456',
            'selfie' => UploadedFile::fake()->image('selfie-ktp.jpg'),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['foto_wajah'])
            ->assertJsonPath('errors.foto_wajah.0', 'Foto wajah / foto diri wajib diunggah.');
        $this->assertDatabaseMissing('mitras', ['phone' => '081234567802']);
        $this->assertSame([], Storage::disk('public')->allFiles('mitra/avatars'));
    }

    public function test_mitra_registration_form_has_separate_face_photo_upload_and_skips_native_hidden_step_validation(): void
    {
        $this->get(route('mitra.daftar'))
            ->assertSee('<form id="formRegistrasiMitra" enctype="multipart/form-data" novalidate', false)
            ->assertSee('id="fileInputFotoWajah"', false)
            ->assertSee('id="reviewFotoWajah"', false);
    }
}
