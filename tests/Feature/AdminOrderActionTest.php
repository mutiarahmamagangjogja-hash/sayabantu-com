<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Order;
use App\Services\AdminRealtimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan admin dapat membuka halaman detail pesanan.
     */
    public function test_admin_can_view_order_detail_page(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.pesanan.detail', 1024));

        $response->assertStatus(200);
        $response->assertSee('#SB1024');
        $response->assertSee('Bersihkan Rumah');
        $response->assertSee('Tindakan Admin');
        $response->assertSee('Konfirmasi Pesanan');
        $response->assertSee('Catatan Admin');
    }

    /**
     * Memastikan admin dapat mengonfirmasi pesanan melalui API.
     */
    public function test_admin_can_confirm_order(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.confirm', 1024));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonPath('data.status', 'Dikonfirmasi');
    }

    /**
     * Memastikan Admin hanya dapat mengonfirmasi pembayaran yang sudah dilaporkan Customer.
     */
    public function test_admin_cannot_confirm_payment_before_customer_reports_it(): void
    {
        $order = Order::factory()->create([
            'status' => 'siap_bayar',
            'escrow_status' => 'none',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson('/admin/api/orders/'.ltrim($order->code, '#').'/confirm-payment', [
                'payment_method' => 'QRIS',
            ]);

        $response->assertStatus(409)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Customer belum melaporkan pembayaran.');

        $this->assertSame('siap_bayar', $order->fresh()->status);
        $this->assertNull($order->fresh()->payment_method);
    }

    public function test_admin_cannot_forward_reported_payment_before_confirming_it(): void
    {
        $order = Order::factory()->create([
            'status' => 'menunggu_konfirmasi_pembayaran',
            'escrow_status' => 'holding',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'payment_verified_at' => null,
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.pembayaran.teruskan', ltrim($order->code, '#')));

        $response->assertStatus(409)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Pembayaran harus dikonfirmasi Admin dan ditahan di escrow sebelum dana diteruskan ke mitra.');

        $this->assertSame('holding', $order->fresh()->escrow_status);
        $this->assertNull($order->fresh()->escrow_released_at);
    }

    public function test_admin_cannot_forward_funds_before_customer_and_admin_confirm_work_completion(): void
    {
        $order = Order::factory()->create([
            'status' => 'berjalan',
            'escrow_status' => 'held',
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.pembayaran.teruskan', ltrim($order->code, '#')));

        $response->assertStatus(409)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Dana otomatis ditahan sampai Customer dan Admin mengonfirmasi pekerjaan selesai.');

        $this->assertSame('berjalan', $order->fresh()->status);
        $this->assertSame('held', $order->fresh()->escrow_status);
        $this->assertNull($order->fresh()->escrow_released_at);

        $paymentPage = $this->withSession(['admin_logged_in' => true])->get(route('admin.pembayaran'));
        $paymentPage->assertSee('Dana Otomatis Ditahan di Escrow')
            ->assertDontSee('Teruskan ke Mitra')
            ->assertDontSee('Tahan Dana');
    }

    public function test_admin_completion_requires_customer_confirmation_and_keeps_funds_held_until_payout(): void
    {
        $order = Order::factory()->create([
            'status' => 'menunggu_konfirmasi_customer',
            'escrow_status' => 'held',
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
        ]);

        $blockedResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.confirm-completion', ltrim($order->code, '#')));

        $blockedResponse->assertStatus(409)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Pekerjaan harus dikonfirmasi selesai oleh Customer sebelum Admin dapat menyelesaikannya.');
        $this->assertSame('menunggu_konfirmasi_customer', $order->fresh()->status);
        $this->assertSame('held', $order->fresh()->escrow_status);

        $customerCompletionResponse = $this->postJson(route('api.orders.customer-selesai', ltrim($order->code, '#')));
        $customerCompletionResponse->assertOk()
            ->assertJsonPath('order.status', 'menunggu_konfirmasi_admin_selesai');

        $completionResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.confirm-completion', ltrim($order->code, '#')));

        $completionResponse->assertOk()
            ->assertJsonPath('status', 'success');
        $order->refresh();
        $this->assertSame('selesai', $order->status);
        $this->assertSame('held', $order->escrow_status);
        $this->assertNull($order->escrow_released_at);

        $paymentPage = $this->withSession(['admin_logged_in' => true])->get(route('admin.pembayaran'));
        $paymentPage->assertSee('Teruskan ke Mitra')
            ->assertDontSee('Tahan Dana');

        $paymentDetail = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.pembayaran.detail', ltrim($order->code, '#')));
        $paymentDetail->assertSee('id="btnTriggerPayout"', false)
            ->assertSee('Teruskan Uang ke Mitra')
            ->assertSee('id="payoutConfirmModal"', false);

        $payoutResponse = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.pembayaran.teruskan', ltrim($order->code, '#')));

        $payoutResponse->assertOk()
            ->assertJsonPath('success', true);
        $this->assertSame('released_to_mitra', $order->fresh()->escrow_status);
        $this->assertNotNull($order->fresh()->escrow_released_at);
    }

    public function test_mitra_balance_only_includes_funds_after_admin_releases_escrow(): void
    {
        $mitra = Mitra::factory()->verified()->create();
        $order = Order::factory()->create([
            'status' => 'selesai',
            'mitra_id' => $mitra->mitra_id,
            'mitra_name' => $mitra->name,
            'nominal' => 50000,
            'biaya' => '50.000',
            'escrow_status' => 'held',
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
        ]);
        Order::factory()->create([
            'status' => 'selesai',
            'mitra_id' => 'SB-OTHER',
            'mitra_name' => 'Mitra Lain',
            'nominal' => 90000,
            'escrow_status' => 'released_to_mitra',
            'mitra_fee' => 81000,
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
            'escrow_released_at' => now(),
        ]);

        $mitraSession = [
            'mitra_auth' => [
                'id' => $mitra->id,
                'mitra_id' => $mitra->mitra_id,
                'name' => $mitra->name,
                'phone' => $mitra->phone,
                'status' => 'approved',
                'isLoggedIn' => true,
            ],
        ];

        $this->withSession($mitraSession)
            ->get(route('mitra.dashboard'))
            ->assertViewHas('saldoMitra', 0)
            ->assertViewHas('saldoMitraHariIni', 0)
            ->assertViewHas('saldoMitraBulanIni', 0)
            ->assertSee('id="mitraHeroSaldoText"', false)
            ->assertSee('>Rp 0</strong>', false);

        $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.pembayaran.teruskan', ltrim($order->code, '#')))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withSession($mitraSession)
            ->get(route('mitra.dashboard'))
            ->assertViewHas('saldoMitra', 45000)
            ->assertViewHas('saldoMitraHariIni', 45000)
            ->assertViewHas('saldoMitraBulanIni', 45000)
            ->assertSee('id="mitraHeroSaldoText"', false)
            ->assertSee('>Rp 45.000</strong>', false)
            ->assertSee('id="mitraSaldoTersediaText"', false);

        $this->getJson(route('api.orders.kebutuhan'))
            ->assertOk()
            ->assertJsonFragment([
                'code' => $order->code,
                'escrow_status' => 'released_to_mitra',
                'mitra_fee' => 45000,
            ]);
    }

    public function test_customer_completion_notifies_admin_and_appears_in_admin_work_queue(): void
    {
        $order = Order::factory()->create([
            'status' => 'menunggu_konfirmasi_customer',
            'escrow_status' => 'held',
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
        ]);
        $unreadBefore = app(AdminRealtimeService::class)->getState()['unread_notifications'];

        $response = $this->postJson(route('api.orders.customer-selesai', ltrim($order->code, '#')));

        $response->assertOk()
            ->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'menunggu_konfirmasi_admin_selesai',
        ]);

        $state = app(AdminRealtimeService::class)->getState();
        $this->assertSame($unreadBefore + 1, $state['unread_notifications']);
        $this->assertSame('Konfirmasi Penyelesaian Menunggu', $state['notifications'][0]['title']);
        $this->assertSame('Pekerjaan menunggu konfirmasi Admin', $state['recent_activities'][0]['title']);

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.dashboard'))
            ->assertSee($order->code)
            ->assertSee('Menunggu konfirmasi akhir Admin');

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.konfirmasi'))
            ->assertSee($order->code)
            ->assertSee('Periksa Pekerjaan');
    }

    public function test_customer_can_review_after_confirming_completion_and_review_is_saved(): void
    {
        $order = Order::factory()->create([
            'status' => 'menunggu_konfirmasi_admin_selesai',
            'mitra_name' => 'Mitra Uji',
            'review_rating' => null,
        ]);

        $response = $this->postJson(route('api.orders.review', ltrim($order->code, '#')), [
            'rating' => 4,
            'comment' => 'Pekerjaan rapi dan tepat waktu.',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('review.rating', 4);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'review_rating' => 4,
            'review_comment' => 'Pekerjaan rapi dan tepat waktu.',
        ]);

        $this->get(route('customer.pesanan.detail', ltrim($order->code, '#')))
            ->assertSee('Ulasan Anda Telah Terkirim')
            ->assertSee('Pekerjaan rapi dan tepat waktu.');
    }

    public function test_customer_cannot_review_before_confirming_completion(): void
    {
        $order = Order::factory()->create([
            'status' => 'berjalan',
            'review_rating' => null,
        ]);

        $response = $this->postJson(route('api.orders.review', ltrim($order->code, '#')), [
            'rating' => 5,
            'comment' => 'Pekerjaan selesai.',
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Ulasan hanya dapat diberikan setelah Customer mengonfirmasi pekerjaan selesai.');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'review_rating' => null,
        ]);
    }

    public function test_customer_order_details_return_404_for_unknown_orders(): void
    {
        $this->get(route('customer.pesanan.detail', 'SB-SURUH-404'))
            ->assertNotFound();
    }

    public function test_customer_order_details_show_actual_mitra_review_metrics(): void
    {
        $mitra = Mitra::factory()->verified()->create();
        Order::factory()->create([
            'mitra_id' => $mitra->mitra_id,
            'mitra_name' => $mitra->name,
            'status' => 'selesai',
            'review_rating' => 4,
        ]);
        $order = Order::factory()->create([
            'mitra_id' => $mitra->mitra_id,
            'mitra_name' => $mitra->name,
            'status' => 'menunggu_mitra',
        ]);

        $this->get(route('customer.pesanan.detail', ltrim($order->code, '#')))
            ->assertSee('4.0')
            ->assertSee('1 ulasan')
            ->assertSee('1 tugas selesai');
    }

    public function test_unassigned_order_does_not_show_an_unrelated_mitra(): void
    {
        Mitra::factory()->verified()->create([
            'name' => 'Mitra Tidak Terhubung',
        ]);
        $order = Order::factory()->create([
            'mitra_id' => null,
            'mitra_phone' => null,
            'mitra_name' => null,
        ]);

        $this->get(route('customer.pesanan.detail', ltrim($order->code, '#')))
            ->assertOk()
            ->assertDontSee('Mitra Tidak Terhubung');
    }

    public function test_mitra_order_status_actions_require_the_previous_workflow_stage(): void
    {
        $order = Order::factory()->create([
            'status' => 'berjalan',
            'escrow_status' => 'held',
            'payment_verified_at' => now(),
        ]);

        $otwResponse = $this->postJson(route('api.orders.mitra-otw', ltrim($order->code, '#')));
        $otwResponse->assertStatus(409);
        $this->assertSame('berjalan', $order->fresh()->status);

        $workResponse = $this->postJson(route('api.orders.mitra-kerja', ltrim($order->code, '#')));
        $workResponse->assertStatus(409);
        $this->assertSame('berjalan', $order->fresh()->status);

        $completionResponse = $this->postJson(route('api.orders.mitra-selesai', ltrim($order->code, '#')));
        $completionResponse->assertStatus(200)
            ->assertJsonPath('order.status', 'menunggu_konfirmasi_customer');
    }

    public function test_admin_simulation_and_order_reset_endpoints_are_removed(): void
    {
        $this->withSession(['admin_logged_in' => true])
            ->postJson('/admin/api/simulate-event')
            ->assertNotFound();

        $this->withSession(['admin_logged_in' => true])
            ->postJson('/admin/api/orders/reset')
            ->assertNotFound();
    }

    public function test_admin_revision_request_is_saved_and_shared_with_both_parties(): void
    {
        $order = Order::factory()->create([
            'status' => 'menunggu_konfirmasi_admin',
            'agreed_price' => '125.000',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('api.orders.admin-revision', ltrim($order->code, '#')), [
                'note' => 'Mohon sesuaikan jadwal pengerjaan.',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'nego',
            'agreed_price' => null,
        ]);
        $this->assertDatabaseHas('chat_messages', [
            'order_code' => ltrim($order->code, '#'),
            'message' => 'Admin meminta revisi kesepakatan: Mohon sesuaikan jadwal pengerjaan.',
        ]);
    }

    public function test_admin_rejection_is_saved_and_shared_with_both_parties(): void
    {
        $order = Order::factory()->create([
            'status' => 'menunggu_konfirmasi_admin',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('api.orders.admin-reject', ltrim($order->code, '#')), [
                'note' => 'Harga tidak sesuai ketentuan.',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'semua_ditolak',
            'cancellation_reason' => 'Harga tidak sesuai ketentuan.',
            'cancelled_by' => 'admin',
        ]);
        $this->assertDatabaseHas('chat_messages', [
            'order_code' => ltrim($order->code, '#'),
            'message' => 'Admin menolak kesepakatan: Harga tidak sesuai ketentuan.',
        ]);
    }

    public function test_customer_dashboard_displays_review_prompt_for_completed_order(): void
    {
        $order = Order::factory()->create([
            'status' => 'selesai',
            'customer_name' => 'Customer Uji',
            'customer_phone' => '081234567890',
            'review_rating' => null,
        ]);

        $this->withSession([
            'customer_name' => 'Customer Uji',
            'customer_phone' => '081234567890',
        ])
            ->get(route('customer.dashboard'))
            ->assertSee($order->code)
            ->assertSee('Beri Ulasan');
    }

    /**
     * Memastikan admin dapat menandai pesanan sebagai konflik dan menyelesaikannya.
     */
    public function test_admin_can_mark_order_as_conflict_and_resolve(): void
    {
        // 1. Tandai konflik
        $conflictRes = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.conflict', 1024), [
                'reason' => 'Mitra belum hadir di lokasi pekerjaan',
            ]);

        $conflictRes->assertStatus(200);
        $conflictRes->assertJsonPath('data.status', 'Konflik');
        $conflictRes->assertJsonPath('data.conflict_reason', 'Mitra belum hadir di lokasi pekerjaan');

        // 2. Selesaikan mediasi konflik
        $resolveRes = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.resolve-conflict', 1024));

        $resolveRes->assertStatus(200);
        $resolveRes->assertJsonPath('data.status', 'Diproses');
        $this->assertNull($resolveRes->json('data.conflict_reason'));
    }

    /**
     * Memastikan endpoint pembatalan konfirmasi pembayaran Admin dinonaktifkan.
     */
    public function test_admin_cannot_unconfirm_customer_payment_from_the_admin_endpoint(): void
    {
        $order = Order::factory()->create([
            'status' => 'terjadwal',
            'escrow_status' => 'held',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson('/admin/api/orders/'.ltrim($order->code, '#').'/unconfirm-payment');

        $response->assertNotFound();
        $this->assertSame('terjadwal', $order->fresh()->status);
        $this->assertSame('held', $order->fresh()->escrow_status);
    }

    /**
     * Memastikan admin dapat membatalkan konfirmasi pesanan (unconfirm order).
     */
    public function test_admin_can_unconfirm_order(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.unconfirm', 1024));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonPath('data.status', 'Diproses');
    }

    /**
     * Memastikan endpoint reset alur pesanan tidak lagi tersedia.
     */
    public function test_admin_cannot_reset_order_flow(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson('/admin/api/orders/1024/reset');

        $response->assertNotFound();
    }

    /**
     * Memastikan admin dapat menyimpan catatan internal pesanan.
     */
    public function test_admin_can_save_order_notes(): void
    {
        $notes = 'Customer meminta ruang tamu dibersihkan terlebih dahulu sebelum area kamar.';
        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.notes', 1024), [
                'notes' => $notes,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $response->assertJsonPath('data.notes', $notes);
    }

    /**
     * Memastikan admin dapat membuka halaman konfirmasi pekerjaan dan tombol Tinjau Aksi tersedia.
     */
    public function test_admin_can_view_konfirmasi_pekerjaan_page(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.konfirmasi'));

        $response->assertStatus(200);
        $response->assertSee('Konfirmasi Pekerjaan');
        $response->assertSee('Status Kesepakatan Kerja');
        $response->assertSee('Tinjau Aksi');
        $response->assertSee('dealDetailModal');
    }

    /**
     * Memastikan admin dapat membuka halaman pengaduan dan modal investigasi serta tombol aksinya tersedia.
     */
    public function test_admin_can_view_pengaduan_page_and_actions(): void
    {
        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.pengaduan'));

        $response->assertStatus(200);
        $response->assertSee('complaintDetailModal');
        $response->assertSee('Mulai Mediasi');
        $response->assertSee('Perlu Bukti');
        $response->assertSee('Tolak Pengaduan');
        $response->assertSee('Ruang Mediasi');
        $response->assertSee('Selesaikan Mediasi');
    }
}
