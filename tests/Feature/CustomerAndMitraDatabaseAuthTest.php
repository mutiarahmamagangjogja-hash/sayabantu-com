<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerAndMitraDatabaseAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_is_stored_in_database(): void
    {
        $response = $this->postJson(route('customer.register'), [
            'name' => 'Budi Customer',
            'phone' => '081234567890',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Budi Customer',
                    'phone' => '081234567890',
                    'role' => 'customer',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Customer',
            'phone' => '081234567890',
            'role' => 'customer',
            'status' => 'aktif',
        ]);
    }

    public function test_customer_registered_in_database_can_login_the_next_day(): void
    {
        User::create([
            'name' => 'Siti Customer',
            'phone' => '081987654321',
            'email' => 'siti@example.com',
            'password' => Hash::make('passwordku123'),
            'role' => 'customer',
            'status' => 'aktif',
        ]);

        $response = $this->postJson(route('customer.login.submit'), [
            'phone' => '081987654321',
            'password' => 'passwordku123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Siti Customer',
                    'phone' => '081987654321',
                    'role' => 'customer',
                ],
            ]);

        $this->assertAuthenticated();
    }

    public function test_unregistered_customer_cannot_login(): void
    {
        $response = $this->postJson(route('customer.login.submit'), [
            'phone' => '089999999999',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertGuest();
    }

    public function test_customer_with_wrong_password_cannot_login(): void
    {
        User::create([
            'name' => 'Doni Customer',
            'phone' => '081211112222',
            'password' => Hash::make('correctpassword'),
            'role' => 'customer',
            'status' => 'aktif',
        ]);

        $response = $this->postJson(route('customer.login.submit'), [
            'phone' => '081211112222',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Kata sandi yang Anda masukkan salah. Silakan coba lagi.',
            ]);

        $this->assertGuest();
    }

    public function test_mitra_can_register_and_is_stored_in_database_with_verifikasi_status(): void
    {
        Storage::fake('public');

        $response = $this->postJson(route('mitra.daftar.submit'), [
            'nama' => 'Agus Pratama',
            'nik' => '3171012345678901',
            'whatsapp' => '081399887766',
            'email' => 'agus@example.com',
            'kota' => 'Jakarta',
            'area' => 'Jakarta Selatan',
            'alamat' => 'Jl. Fatmawati No. 10',
            'pin' => '654321',
            'keahlian' => 'Servis AC & Perbaikan Rumah',
            'jaminan' => 'KTP Asli',
            'selfie' => UploadedFile::fake()->image('selfie-ktp.jpg'),
            'foto_wajah' => UploadedFile::fake()->image('foto-diri.jpg'),
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'pending_approval',
                'mitra' => [
                    'nama' => 'Agus Pratama',
                    'whatsapp' => '081399887766',
                    'kota' => 'Jakarta',
                    'status' => 'verifikasi',
                ],
            ]);

        $this->assertDatabaseHas('mitras', [
            'name' => 'Agus Pratama',
            'phone' => '081399887766',
            'kota' => 'Jakarta',
            'status' => 'verifikasi',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
        ]);
    }

    public function test_mitra_cannot_login_before_admin_acc(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'SB-7788',
            'name' => 'Joko Mitra',
            'phone' => '081555554444',
            'pin' => Hash::make('123456'),
            'kota' => 'Bogor',
            'status' => 'pending_approval',
            'status_label' => 'Pendaftaran Sedang Ditinjau Admin',
        ]);

        $response = $this->postJson(route('mitra.login.submit'), [
            'identifier' => 'SB-7788',
            'pin' => '123456',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'status' => 'pending_approval',
            ]);
    }

    public function test_admin_can_acc_mitra_in_database_and_mitra_can_then_login(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'SB-9900',
            'name' => 'Bambang Santoso',
            'phone' => '081777778888',
            'pin' => Hash::make('123456'),
            'kota' => 'Jakarta',
            'status' => 'pending_approval',
            'status_label' => 'Pendaftaran Sedang Ditinjau Admin',
        ]);

        // Login as admin and call acc endpoint
        $accResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user' => ['email' => 'admin@sayabantu.com', 'role' => 'admin'],
        ])->postJson(route('admin.api.mitras.acc', ['id' => $mitra->id]));

        $accResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('mitras', [
            'id' => $mitra->id,
            'status' => 'approved',
            'status_label' => 'Disetujui & Aktif',
        ]);

        // Now mitra logs in with their credentials
        $loginResponse = $this->postJson(route('mitra.login.submit'), [
            'identifier' => '081777778888',
            'pin' => '123456',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'mitra' => [
                    'name' => 'Bambang Santoso',
                    'status' => 'approved',
                ],
            ]);
    }

    public function test_verified_mitra_in_database_appears_in_mitra_per_kota(): void
    {
        Mitra::create([
            'mitra_id' => 'SB-1122',
            'name' => 'Mitra Jakarta Terverifikasi',
            'phone' => '081888889999',
            'pin' => Hash::make('123456'),
            'kota' => 'Jakarta',
            'keahlian' => 'Full Home Cleaning',
            'status' => 'aktif',
            'status_label' => 'Aktif Terverifikasi',
        ]);

        Mitra::create([
            'mitra_id' => 'SB-3344',
            'name' => 'Mitra Belum Diverifikasi',
            'phone' => '081666667777',
            'pin' => Hash::make('123456'),
            'kota' => 'Jakarta',
            'status' => 'verifikasi',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
        ]);

        $response = $this->getJson(route('api.mitra.per-kota'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 1,
            ]);

        $data = $response->json('mitras');
        $this->assertCount(1, $data);
        $this->assertEquals('Mitra Jakarta Terverifikasi', $data[0]['nama']);
    }

    /**
     * Memastikan judul halaman tetap 'Data Pengguna' dan data customer dapat diakses.
     */
    public function test_admin_can_view_data_pengguna_page_with_title_and_customers(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.pengguna'));

        $response->assertStatus(200);
        $response->assertSee('Data Pengguna');
        $response->assertSee('Customer');
        $response->assertSee('Mitra');
        $response->assertSee('adminCustomerListContainer');
    }

    /**
     * Memastikan judul halaman data mitra tetap 'Data Pengguna' tanpa berubah.
     */
    public function test_admin_can_view_data_mitra_page_with_consistent_title(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.mitra'));

        $response->assertStatus(200);
        $response->assertSee('Data Pengguna');
        $response->assertSee('Customer');
        $response->assertSee('Mitra');
        $response->assertSee('adminMitraListContainer');
    }

    /**
     * Memastikan API daftar customer mengembalikan data pengguna nyata.
     */
    public function test_admin_can_fetch_customer_list_api(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->getJson(route('admin.api.customers.list'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
        $response->assertJsonStructure([
            'success',
            'customers',
            'counts' => ['total', 'aktif', 'terverifikasi', 'baru'],
        ]);
    }

    /**
     * Memastikan mitra dapat login menggunakan Nama, Nomor HP, atau ID Mitra.
     */
    public function test_mitra_can_login_by_name_phone_or_mitra_id(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'SB-8821',
            'name' => 'Salsa Mitra',
            'phone' => '081234567890',
            'pin' => Hash::make('123456'),
            'status' => 'approved',
            'status_label' => 'Disetujui & Aktif',
        ]);

        // Login dengan nama (lowercase)
        $respName = $this->postJson(route('mitra.login.submit'), [
            'identifier' => 'salsa mitra',
            'pin' => '123456',
        ]);
        $respName->assertOk()
            ->assertJson(['success' => true, 'status' => 'approved']);

        // Login dengan nomor telepon format 8... (tanpa 0)
        $respPhone = $this->postJson(route('mitra.login.submit'), [
            'identifier' => '81234567890',
            'pin' => '123456',
        ]);
        $respPhone->assertOk()
            ->assertJson(['success' => true, 'status' => 'approved']);

        // Login dengan ID Mitra tanpa strip 'SB8821'
        $respId = $this->postJson(route('mitra.login.submit'), [
            'identifier' => 'SB8821',
            'pin' => '123456',
        ]);
        $respId->assertOk()
            ->assertJson(['success' => true, 'status' => 'approved']);
    }

    /**
     * Memastikan halaman dashboard publik menampilkan Form Ajukan Suruhan dan tidak mencantumkan daftar mitra.
     */
    public function test_public_dashboard_displays_order_form_and_does_not_display_mitra_list(): void
    {
        Mitra::create([
            'mitra_id' => 'SB-9999',
            'name' => 'Mitra Jangan Tampil Di Beranda',
            'phone' => '081299998888',
            'pin' => Hash::make('123456'),
            'status' => 'approved',
            'status_label' => 'Disetujui & Aktif',
        ]);

        $response = $this->get(route('customer.home'));

        $response->assertOk();
        // Form suruhan harus tetap tampil
        $response->assertSee('Form Ajukan Suruhan');
        $response->assertSee('orderFormCard');

        // Mitra tidak dicantumkan di dashboard
        $response->assertDontSee('Mitra Terverifikasi di Wilayah Anda');
        $response->assertDontSee('Mitra Jangan Tampil Di Beranda');
        $response->assertDontSee('Adam Julian Tjiu');
    }

    /**
     * Memastikan alur login mitra mengarah ke dashboard tanpa ada script redirect kickback di <head>.
     */
    public function test_mitra_login_leads_to_mitra_dashboard_without_head_redirect_kickback(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'SB-7777',
            'name' => 'Mitra Sukses Masuk',
            'phone' => '081277778888',
            'pin' => Hash::make('123456'),
            'status' => 'approved',
            'status_label' => 'Disetujui & Aktif',
        ]);

        $loginResp = $this->postJson(route('mitra.login.submit'), [
            'identifier' => '081277778888',
            'pin' => '123456',
        ]);

        $loginResp->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'approved',
                'redirect' => route('mitra.dashboard'),
            ]);

        // Dashboard request with the session from login
        $dashboardResp = $this->get(route('mitra.dashboard'));
        $dashboardResp->assertOk();
        $dashboardResp->assertSee('Mitra Sukses Masuk');

        // Verify <head> does not contain the broken kickback script
        $content = $dashboardResp->getContent();
        $head = substr($content, 0, strpos($content, '</head>'));
        $this->assertStringNotContainsString('window.location.replace', $head);
    }
}
