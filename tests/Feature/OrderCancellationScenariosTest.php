<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderCancellationScenariosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario A: Admin Rejection of Mitra
     * Admin menolak berkas mitra dengan alasan dan opsi izinkan kirim ulang data perbaikan (can_reapply).
     */
    public function test_scenario_a_admin_rejects_mitra_registration_with_reason_and_reapply_option(): void
    {
        // 1. Mitra mendaftar
        $mitra = Mitra::create([
            'mitra_id' => 'SB-1001',
            'name' => 'Bambang Sudrajat',
            'phone' => '081233445566',
            'pin' => Hash::make('654321'),
            'kota' => 'Jakarta Timur',
            'status' => 'pending_approval',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
            'penalty_score' => 0,
        ]);

        // 2. Admin menolak pendaftaran dengan alasan dan izinkan kirim ulang
        $rejectResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.mitras.tolak', ['id' => $mitra->id]), [
                'reason' => 'Foto KTP buram dan surat jaminan tidak terbaca.',
                'can_reapply' => true,
            ]);

        $rejectResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'can_reapply' => true,
                'mitra' => [
                    'status' => 'rejected',
                ],
            ]);

        $this->assertDatabaseHas('mitras', [
            'id' => $mitra->id,
            'status' => 'rejected',
            'rejection_reason' => 'Foto KTP buram dan surat jaminan tidak terbaca.',
            'can_reapply' => 1,
        ]);

        // 3. Mitra login -> mendeteksi status rejected dengan heading dan alasan
        $loginResponse = $this->postJson(route('mitra.login.submit'), [
            'identifier' => $mitra->phone,
            'pin' => '654321',
        ]);

        $loginResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
                'status' => 'rejected',
                'heading' => 'Pendaftaran Belum Dapat Disetujui',
                'can_reapply' => true,
                'redirect' => route('mitra.pending'),
            ]);

        // 4. Mitra mengakses halaman pending/rejection -> menampilkan pesan penolakan dan tombol perbaikan
        $pendingView = $this->withSession([
            'mitra_auth' => [
                'isLoggedIn' => false,
                'id' => $mitra->id,
                'phone' => $mitra->phone,
                'status' => 'rejected',
                'rejection_reason' => 'Foto KTP buram dan surat jaminan tidak terbaca.',
                'can_reapply' => true,
            ],
        ])->get(route('mitra.pending'));

        $pendingView->assertStatus(200);
        $pendingView->assertSee('Pendaftaran Belum Dapat Disetujui');
        $pendingView->assertSee('Foto KTP buram dan surat jaminan tidak terbaca.');
        $pendingView->assertSee('Ajukan Perbaikan / Unggah Ulang Berkas');
    }

    /**
     * Skenario B: Admin Cancels Order
     * Admin menolak/membatalkan pesanan dengan alasan wajib dan dana escrow dikembalikan (refunded).
     */
    public function test_scenario_b_admin_cancels_order_with_mandatory_reason_and_refunds_escrow(): void
    {
        $order = Order::create([
            'code' => 'SB-9901',
            'customer_name' => 'Rina Melati',
            'customer_phone' => '081299887766',
            'layanan' => 'Antar Dokumen Penting',
            'jadwal' => 'Hari Ini',
            'alamat' => 'Sudirman Central Business District',
            'biaya' => 'Rp 120.000',
            'nominal' => 120000,
            'metode' => 'QRIS',
            'status' => 'siap_bayar',
            'escrow_status' => 'held',
            'refund_amount' => 0,
        ]);

        $cancelResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.cancel', ['id' => $order->id]), [
                'reason' => 'Pesanan terindikasi transaksi berisiko tinggi dan alamat fiktif.',
            ]);

        $cancelResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled_by_admin',
            'cancelled_by' => 'admin',
            'cancellation_reason' => 'Pesanan terindikasi transaksi berisiko tinggi dan alamat fiktif.',
            'escrow_status' => 'refunded',
            'refund_amount' => 120000,
        ]);
    }

    /**
     * Skenario C: Mitra Cancels Order After Agreement
     * Mitra membatalkan tugas yang sudah disepakati: pesanan kembali disiarkan (waiting_for_partner)
     * dan mitra dikenai penalti performa otomatis (+10 penalty_score).
     */
    public function test_scenario_c_mitra_cancels_task_after_agreement_with_performance_penalty(): void
    {
        $mitra = Mitra::create([
            'mitra_id' => 'SB-2002',
            'name' => 'Agus Handoko',
            'phone' => '087799881122',
            'pin' => Hash::make('123456'),
            'kota' => 'Bandung',
            'status' => 'approved',
            'penalty_score' => 0,
        ]);

        $order = Order::create([
            'code' => 'SB-9902',
            'customer_name' => 'Dewi Lestari',
            'customer_phone' => '081388776655',
            'layanan' => 'Perbaikan Pipa Bocor',
            'jadwal' => 'Besok Siang',
            'alamat' => 'Dago Atas No. 45',
            'biaya' => 'Rp 95.000',
            'nominal' => 95000,
            'metode' => 'Saldo SayaBantu',
            'status' => 'terjadwal',
            'mitra_name' => 'Agus Handoko',
            'escrow_status' => 'held',
        ]);

        $cancelResponse = $this->withSession([
            'mitra_auth' => [
                'isLoggedIn' => true,
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'phone' => $mitra->phone,
                'status' => 'approved',
            ],
        ])->postJson(route('api.orders.mitra-cancel', ['code' => $order->code]), [
            'reason' => 'Kecelakaan ringan di perjalanan menuju lokasi customer.',
        ]);

        $cancelResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Cek penalti bertambah 10
        $mitra->refresh();
        $this->assertEquals(10, $mitra->penalty_score);

        // Cek order kembali ke antrean mitra lain
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'waiting_for_partner',
            'mitra_name' => null,
        ]);

        $order->refresh();
        $this->assertStringContainsString('Kecelakaan ringan', $order->cancellation_reason);
    }

    /**
     * Skenario D: Customer Cancels Order
     * - Sebelum kesepakatan mitra (steps 4-5): langsung dibatalkan tanpa refund escrow
     * - Setelah kesepakatan mitra (steps 6-10): dibatalkan dengan pengembalian dana escrow (refunded)
     */
    public function test_scenario_d_customer_cancels_before_and_after_agreement(): void
    {
        // 1. Pembatalan sebelum kesepakatan (status menunggu)
        $orderPre = Order::create([
            'code' => 'SB-PRE-1',
            'customer_name' => 'Hendra Setiawan',
            'customer_phone' => '081122334455',
            'layanan' => 'Bersihkan Ruang Tamu',
            'jadwal' => 'Hari Ini',
            'alamat' => 'Kebayoran Baru',
            'biaya' => 'Rp 75.000',
            'nominal' => 75000,
            'metode' => 'Tunai',
            'status' => 'menunggu',
            'escrow_status' => 'none',
        ]);

        $resPre = $this->postJson(route('api.orders.customer-cancel', ['code' => $orderPre->code]), [
            'reason' => 'Menemukan bantuan dari rekan sendiri.',
        ]);

        $resPre->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_agreement' => false,
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderPre->id,
            'status' => 'cancelled_by_customer',
            'cancelled_by' => 'customer',
            'escrow_status' => 'none',
        ]);

        // 2. Pembatalan setelah kesepakatan (status terjadwal dengan escrow held)
        $orderPost = Order::create([
            'code' => 'SB-POST-2',
            'customer_name' => 'Hendra Setiawan',
            'customer_phone' => '081122334455',
            'layanan' => 'Jaga Toko Harian',
            'jadwal' => 'Besok Pagi',
            'alamat' => 'Pasar Baru Blok A',
            'biaya' => 'Rp 150.000',
            'nominal' => 150000,
            'metode' => 'QRIS',
            'status' => 'terjadwal',
            'escrow_status' => 'held',
            'refund_amount' => 0,
        ]);

        $resPost = $this->postJson(route('api.orders.customer-cancel', ['code' => $orderPost->code]), [
            'reason' => 'Toko tutup mendadak karena ada renovasi darurat.',
        ]);

        $resPost->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_agreement' => true,
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderPost->id,
            'status' => 'cancelled_by_customer',
            'cancelled_by' => 'customer',
            'cancellation_reason' => 'Toko tutup mendadak karena ada renovasi darurat.',
            'escrow_status' => 'refunded',
            'refund_amount' => 150000,
        ]);
    }
}
