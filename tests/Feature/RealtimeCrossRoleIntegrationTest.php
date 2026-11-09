<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Order;
use App\Models\User;
use App\Services\AdminRealtimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealtimeCrossRoleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_laporan_analitik_displays_real_customer_and_mitra_data(): void
    {
        // Buat 3 customer riil di database
        User::create(['name' => 'Customer Satu', 'phone' => '08111111111', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'aktif']);
        User::create(['name' => 'Customer Dua', 'phone' => '08222222222', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'aktif']);
        User::create(['name' => 'Customer Tiga', 'phone' => '08333333333', 'password' => bcrypt('123'), 'role' => 'customer', 'status' => 'aktif']);

        // Buat 2 mitra riil di database
        Mitra::create([
            'mitra_id' => 'SB-0001',
            'name' => 'Mitra Budi',
            'phone' => '08444444444',
            'pin' => bcrypt('123456'),
            'kota' => 'Jakarta Selatan',
            'keahlian' => 'Full Home Cleaning',
            'status' => 'aktif',
            'status_label' => 'Aktif Terverifikasi',
        ]);
        Mitra::create([
            'mitra_id' => 'SB-0002',
            'name' => 'Mitra Joko',
            'phone' => '08555555555',
            'pin' => bcrypt('123456'),
            'kota' => 'Bandung',
            'keahlian' => 'Belanja & Antar Barang',
            'status' => 'verifikasi',
            'status_label' => 'Menunggu Verifikasi & ACC',
        ]);

        // Login sebagai Admin
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.laporan'));

        $response->assertStatus(200);

        // Pastikan total customer (3) dan total mitra (2) muncul di view
        $response->assertViewHas('totalCustomers', 3);
        $response->assertViewHas('totalMitras', 2);
        $response->assertViewHas('verifiedMitras', 1);
        $response->assertViewHas('pendingMitras', 1);
        $response->assertSee('TOTAL CUSTOMER');
        $response->assertSee('TOTAL MITRA');
    }

    public function test_customer_can_create_suruhan_and_it_is_stored_in_database(): void
    {
        $response = $this->postJson(route('api.orders.store'), [
            'layanan' => 'Full Home Cleaning',
            'alamat' => 'Jl. Kebon Jeruk No. 8, Jakarta Barat',
            'budget' => '120.000',
            'metode' => 'Nego',
            'tanggal' => '2026-10-28',
            'jam' => '10:00 WIB',
            'customer_name' => 'Rina Angelina',
            'customer_phone' => '081299887766',
            'note' => 'Bawa alat pel dan lap kaca',
            'gender' => 'Wanita',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'layanan' => 'Full Home Cleaning',
                    'alamat' => 'Jl. Kebon Jeruk No. 8, Jakarta Barat',
                    'status' => 'nego',
                    'customerName' => 'Rina Angelina',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'layanan' => 'Full Home Cleaning',
            'customer_name' => 'Rina Angelina',
            'alamat' => 'Jl. Kebon Jeruk No. 8, Jakarta Barat',
            'status' => 'nego',
        ]);

        // Verifikasi realtime state admin ter-update
        $state = app(AdminRealtimeService::class)->getState();
        $this->assertGreaterThanOrEqual(1, $state['stats']['total_pesanan']);
        $this->assertNotEmpty($state['recent_orders']);
    }

    public function test_customer_suruhan_appears_in_kebutuhan_mitra_api(): void
    {
        Order::create([
            'code' => '#SB-SURUH-7788',
            'layanan' => 'Antar Barang',
            'customer_name' => 'Ahmad Syahril',
            'customer_phone' => '081312345678',
            'alamat' => 'Jl. Tebet Raya No. 45, Jakarta Selatan',
            'jadwal' => 'Besok • 14:00 WIB',
            'biaya' => '75.000',
            'nominal' => 75000,
            'metode' => 'Pas',
            'status' => 'menunggu_mitra',
            'step' => 4,
        ]);

        $response = $this->getJson(route('api.orders.kebutuhan'));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonFragment([
                'code' => '#SB-SURUH-7788',
                'layanan' => 'Antar Barang',
                'customerName' => 'Ahmad Syahril',
            ]);
    }

    public function test_customer_suruhan_appears_in_admin_orders_api(): void
    {
        Order::create([
            'code' => '#SB-SURUH-9911',
            'layanan' => 'Bantu Pindahan Kos',
            'customer_name' => 'Dewi Lestari',
            'customer_phone' => '081233445566',
            'alamat' => 'Jl. Margonda Raya No. 100, Depok',
            'jadwal' => 'Hari ini • 15:30 WIB',
            'biaya' => '150.000',
            'nominal' => 150000,
            'metode' => 'Nego',
            'status' => 'nego',
            'step' => 5,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->getJson(route('admin.api.orders.list'));

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonFragment([
                'code' => '#SB-SURUH-9911',
                'layanan' => 'Bantu Pindahan Kos',
                'customerName' => 'Dewi Lestari',
            ]);
    }

    public function test_mitra_registration_appears_in_admin_and_can_be_approved(): void
    {
        Storage::fake('public');

        // 1. Mitra mendaftar
        $regResponse = $this->postJson(route('mitra.daftar.submit'), [
            'nama' => 'Fajar Santoso',
            'whatsapp' => '087711223344',
            'email' => 'fajar@example.com',
            'kota' => 'Jakarta Timur',
            'area' => 'Cakung, Duren Sawit',
            'alamat' => 'Jl. Raden Inten No. 12',
            'keahlian' => 'Pindahan & Angkut Barang',
            'jaminan' => 'KTP & Ijazah Asli',
            'pin' => '123456',
            'selfie' => UploadedFile::fake()->image('fajar.jpg'),
            'foto_wajah' => UploadedFile::fake()->image('fajar-diri.jpg'),
        ]);

        $regResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('mitras', [
            'name' => 'Fajar Santoso',
            'phone' => '087711223344',
            'status' => 'verifikasi',
        ]);

        $mitra = Mitra::where('phone', '087711223344')->first();
        $this->assertNotNull($mitra);

        // 2. Muncul di API admin mitra
        $adminListResponse = $this->withSession(['admin_logged_in' => true])
            ->getJson(route('admin.api.mitras.list'));

        $adminListResponse->assertStatus(200)
            ->assertJsonFragment([
                'nama' => 'Fajar Santoso',
                'status' => 'verifikasi',
            ]);

        // 3. Admin ACC mitra
        $accResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.mitras.acc', ['id' => $mitra->id]));

        $accResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'mitra' => [
                    'status' => 'approved',
                ],
            ]);

        $this->assertDatabaseHas('mitras', [
            'id' => $mitra->id,
            'status' => 'approved',
            'status_label' => 'Disetujui & Aktif',
        ]);
    }

    public function test_unverified_mitra_cannot_access_mitra_dashboard_until_admin_approves(): void
    {
        // Mitra mendaftar
        $mitra = Mitra::create([
            'mitra_id' => 'SB-7711',
            'name' => 'Calon Mitra Baru',
            'phone' => '087788990011',
            'pin' => bcrypt('123456'),
            'kota' => 'Jakarta Selatan',
            'status' => 'pending_approval',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
        ]);

        // Coba akses dashboard dengan session pending_approval (belum di-ACC) -> dialihkan ke pending
        $dashResponse = $this->withSession([
            'mitra_auth' => [
                'isLoggedIn' => false,
                'mitra_id' => $mitra->mitra_id,
                'phone' => $mitra->phone,
                'status' => 'pending_approval',
            ],
        ])->get(route('mitra.dashboard'));

        $dashResponse->assertRedirect(route('mitra.pending'));

        // Cek API status mitra mengembalikan isLoggedIn = false
        $statusApi = $this->getJson(route('api.mitra.status', ['identifier' => $mitra->phone]));
        $statusApi->assertStatus(200)
            ->assertJson([
                'isLoggedIn' => false,
                'status' => 'pending_approval',
            ]);

        // Admin melakukan ACC / verifikasi
        $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.mitras.acc', ['id' => $mitra->id]))
            ->assertStatus(200);

        // Setelah di-ACC, status API mengembalikan isLoggedIn = true
        $statusApiAfter = $this->getJson(route('api.mitra.status', ['identifier' => $mitra->phone]));
        $statusApiAfter->assertStatus(200)
            ->assertJson([
                'isLoggedIn' => true,
                'status' => 'approved',
            ]);

        // Mitra login resmi dengan PIN
        $loginResponse = $this->postJson(route('mitra.login.submit'), [
            'identifier' => $mitra->phone,
            'pin' => '123456',
        ]);
        $loginResponse->assertStatus(200);

        // Akses dashboard sukses (200 OK)
        $dashSuccessResponse = $this->withSession([
            'mitra_auth' => [
                'isLoggedIn' => true,
                'mitra_id' => $mitra->mitra_id,
                'phone' => $mitra->phone,
                'status' => 'approved',
            ],
        ])->get(route('mitra.dashboard'));

        $dashSuccessResponse->assertStatus(200);
    }
}
