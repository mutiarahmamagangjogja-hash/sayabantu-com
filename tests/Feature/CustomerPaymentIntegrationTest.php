<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\AdminRealtimeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_payment_report_is_persisted_but_waits_for_admin_confirmation(): void
    {
        $order = Order::factory()->create([
            'code' => '#SB-SURUH-4321',
            'status' => 'siap_bayar',
            'escrow_status' => 'none',
        ]);

        $response = $this->postJson(route('api.orders.customer-pay', 'SB-SURUH-4321'), [
            'metode' => 'BCA Virtual Account',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order.status', 'menunggu_konfirmasi_pembayaran')
            ->assertJsonPath('order.escrow_status', 'holding')
            ->assertJsonPath('order.payment_method', 'BCA Virtual Account');

        $order->refresh();
        $this->assertSame('menunggu_konfirmasi_pembayaran', $order->status);
        $this->assertSame('holding', $order->escrow_status);
        $this->assertSame('BCA Virtual Account', $order->payment_method);
        $this->assertNotNull($order->payment_submitted_at);
        $this->assertNull($order->payment_verified_at);

        $customerPage = $this->get(route('customer.pembayaran', 'SB-SURUH-4321'));
        $customerPage->assertSee('BCA Virtual Account')
            ->assertSee('Menunggu Konfirmasi Admin')
            ->assertSee('id="pembayaranTahap4" class="space-y-3.5 hidden"', false);

        $adminState = app(AdminRealtimeService::class)->getState();
        $this->assertSame('Customer melaporkan pembayaran', $adminState['recent_activities'][0]['title']);
        $this->assertSame(1, $adminState['stats']['payment_pending']);
        $this->assertSame('Rp 0', $adminState['stats']['admin_balance']);
    }

    public function test_admin_payment_queue_lists_reports_without_adding_them_to_balance_before_verification(): void
    {
        Order::factory()->create([
            'code' => '#SB-SURUH-4001',
            'status' => 'menunggu_mitra',
            'escrow_status' => 'none',
        ]);
        Order::factory()->create([
            'code' => '#SB-SURUH-4002',
            'status' => 'menunggu_konfirmasi_pembayaran',
            'escrow_status' => 'holding',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'nominal' => 126000,
            'biaya' => '126.000',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.pembayaran'));

        $response->assertSee('#SB-SURUH-4002')
            ->assertSee('Menunggu Konfirmasi Admin')
            ->assertSee('QRIS')
            ->assertDontSee('#SB-SURUH-4001')
            ->assertDontSee('Tidak ada pembayaran')
            ->assertViewHas('countSemua', 1)
            ->assertViewHas('countMenunggu', 1)
            ->assertViewHas('saldoAdmin', 0)
            ->assertViewHas('totalDanaMasuk', 0)
            ->assertViewHas('totalDanaDitahan', 0);

        $detailResponse = $this->get(route('admin.pembayaran.detail', 'SB-SURUH-4002'));
        $detailResponse->assertSee('Menunggu Konfirmasi Admin')
            ->assertSee('QRIS')
            ->assertDontSee('Konfirmasi Dana Sudah Diterima');

        $orderDetailResponse = $this->get(route('admin.pesanan.detail', 'SB-SURUH-4002'));
        $orderDetailResponse->assertSee('Menunggu Konfirmasi Admin')
            ->assertSee('Konfirmasi Pembayaran Customer');
    }

    public function test_reported_payment_does_not_count_as_verified_if_order_status_advances_without_admin_confirmation(): void
    {
        Order::factory()->create([
            'code' => '#SB-SURUH-4011',
            'status' => 'terjadwal',
            'escrow_status' => 'held',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'payment_verified_at' => null,
            'nominal' => 126000,
            'biaya' => '126.000',
        ]);

        $paymentPage = $this->withSession(['admin_logged_in' => true])->get(route('admin.pembayaran'));
        $paymentPage->assertSee('Menunggu konfirmasi Admin')
            ->assertViewHas('saldoAdmin', 0)
            ->assertViewHas('totalDanaMasuk', 0)
            ->assertViewHas('countMenunggu', 1);

        $adminState = app(AdminRealtimeService::class)->getState();
        $this->assertSame(1, $adminState['stats']['payment_pending']);
        $this->assertSame(0, $adminState['stats']['total_pembayaran']);
        $this->assertSame('Rp 0', $adminState['stats']['admin_balance']);
    }

    public function test_admin_confirmation_moves_reported_payment_into_balance(): void
    {
        $order = Order::factory()->create([
            'code' => '#SB-SURUH-4155',
            'status' => 'menunggu_konfirmasi_pembayaran',
            'escrow_status' => 'holding',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'nominal' => 126000,
            'biaya' => '126.000',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])
            ->postJson(route('admin.api.orders.confirm-payment', 'SB-SURUH-4155'));

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.status', 'terjadwal');

        $order->refresh();
        $this->assertSame('terjadwal', $order->status);
        $this->assertSame('held', $order->escrow_status);
        $this->assertSame('QRIS', $order->payment_method);
        $this->assertNotNull($order->payment_verified_at);

        $paymentPage = $this->withSession(['admin_logged_in' => true])->get(route('admin.pembayaran'));
        $paymentPage->assertViewHas('saldoAdmin', 12600)
            ->assertViewHas('totalDanaMasuk', 126000)
            ->assertViewHas('countMenunggu', 0);

        $adminState = app(AdminRealtimeService::class)->getState();
        $this->assertSame('Rp 12.600', $adminState['stats']['admin_balance']);

        $customerPage = $this->get(route('customer.pembayaran', 'SB-SURUH-4155'));
        $customerPage->assertSee('Pembayaran Berhasil Dikonfirmasi!')
            ->assertSee('Dikonfirmasi Admin');
    }

    public function test_repeat_customer_payment_report_keeps_first_method_and_does_not_add_to_balance(): void
    {
        $order = Order::factory()->create([
            'code' => '#SB-SURUH-4123',
            'status' => 'menunggu_konfirmasi_pembayaran',
            'escrow_status' => 'holding',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'nominal' => 126000,
            'biaya' => '126.000',
        ]);

        $response = $this->postJson(route('api.orders.customer-pay', 'SB-SURUH-4123'), [
            'metode' => 'BCA Virtual Account',
        ]);
        $response->assertOk()
            ->assertJsonPath('order.status', 'menunggu_konfirmasi_pembayaran')
            ->assertJsonPath('order.payment_method', 'QRIS');

        $order->refresh();
        $this->assertSame('menunggu_konfirmasi_pembayaran', $order->status);
        $this->assertSame('holding', $order->escrow_status);
        $this->assertSame('QRIS', $order->payment_method);
        $this->assertNull($order->payment_verified_at);

        $paymentPage = $this->get(route('admin.pembayaran'));
        $paymentPage->assertSee('Rp 126.000')
            ->assertViewHas('saldoAdmin', 0)
            ->assertViewHas('totalDanaMasuk', 0);

        $adminState = app(AdminRealtimeService::class)->getState();
        $this->assertSame('Rp 0', $adminState['stats']['admin_balance']);

        $customerResponse = $this->getJson(route('api.orders.show', 'SB-SURUH-4123'));
        $customerResponse->assertOk()
            ->assertJsonPath('order.status', 'menunggu_konfirmasi_pembayaran');

        $this->assertSame('menunggu_konfirmasi_pembayaran', $order->fresh()->status);
        $this->assertSame('holding', $order->fresh()->escrow_status);
    }

    public function test_customer_must_select_a_payment_method_before_confirming_payment(): void
    {
        $order = Order::factory()->create([
            'code' => '#SB-SURUH-4777',
            'status' => 'siap_bayar',
            'escrow_status' => 'none',
        ]);

        $response = $this->postJson(route('api.orders.customer-pay', 'SB-SURUH-4777'), []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('metode');
        $this->assertSame('siap_bayar', $order->fresh()->status);
        $this->assertNull($order->fresh()->payment_method);
        $this->assertNull($order->fresh()->payment_submitted_at);
    }

    public function test_customer_cannot_confirm_payment_through_the_admin_endpoint(): void
    {
        $order = Order::factory()->create([
            'code' => '#SB-SURUH-4555',
            'status' => 'menunggu_konfirmasi_pembayaran',
            'escrow_status' => 'holding',
        ]);

        $response = $this->postJson('/api/orders/SB-SURUH-4555/admin-confirm-payment');

        $response->assertNotFound();
        $this->assertSame('menunggu_konfirmasi_pembayaran', $order->fresh()->status);
        $this->assertSame('holding', $order->fresh()->escrow_status);
    }

    public function test_customer_cannot_report_payment_for_a_cancelled_order(): void
    {
        $order = Order::factory()->create([
            'code' => '#SB-SURUH-4666',
            'status' => 'cancelled_by_customer',
            'escrow_status' => 'refunded',
        ]);

        $response = $this->postJson(route('api.orders.customer-pay', 'SB-SURUH-4666'), [
            'metode' => 'QRIS',
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('success', false);
        $this->assertSame('cancelled_by_customer', $order->fresh()->status);
        $this->assertSame('refunded', $order->fresh()->escrow_status);
        $this->assertNull($order->fresh()->payment_submitted_at);
    }

    public function test_refunded_customer_payment_is_excluded_from_admin_balance(): void
    {
        Order::factory()->create([
            'code' => '#SB-SURUH-4888',
            'status' => 'cancelled_by_customer',
            'escrow_status' => 'refunded',
            'payment_method' => 'QRIS',
            'payment_submitted_at' => now(),
            'payment_verified_at' => now(),
            'refund_amount' => 126000,
            'nominal' => 126000,
            'biaya' => '126.000',
        ]);

        $paymentPage = $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.pembayaran'));

        $paymentPage->assertSee('Dikembalikan ke Customer (Refund)')
            ->assertViewHas('saldoAdmin', 0)
            ->assertViewHas('totalDanaMasuk', 0)
            ->assertViewHas('countGagal', 1);

        $customerPage = $this->get(route('customer.pembayaran', 'SB-SURUH-4888'));
        $customerPage->assertDontSee('Pembayaran Berhasil Dikonfirmasi!');

        $detailPage = $this->get(route('admin.pembayaran.detail', 'SB-SURUH-4888'));
        $detailPage->assertSee('Dana telah dikembalikan');

        $adminState = app(AdminRealtimeService::class)->getState();
        $this->assertSame('Rp 0', $adminState['stats']['admin_balance']);
    }

    public function test_voucher_query_parameters_do_not_change_customer_payment_total(): void
    {
        $response = $this->get(route('customer.pembayaran', 'SB-SURUH-4901').'?total=100.000&metode=Pas&voucher=HEMAT50&diskon=50.000');

        $response->assertViewHas('totalBayar', '100.000')
            ->assertDontSee('Voucher')
            ->assertDontSee('Diskon voucher');
    }

    public function test_customer_and_admin_voucher_pages_are_removed(): void
    {
        $this->get('/customer/voucher')->assertNotFound();
        $this->get('/admin/voucher')->assertNotFound();
    }
}
