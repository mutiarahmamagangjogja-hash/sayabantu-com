<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderFormUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_order_with_direct_price_and_default_gender_bebas(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'name' => 'Tiara Penguji',
            'phone' => '08123456789',
        ]);

        $response = $this->actingAs($user)->postJson(route('api.orders.store'), [
            'layanan' => 'Full Home Cleaning',
            'alamat' => 'Jl. Anggrek No. 10, Jakarta Selatan',
            'budget' => '35.000',
            'tanggal' => date('Y-m-d'),
            'jam' => '14:30 WIB',
            'note' => 'Pembersihan kamar tidur dan ruang tamu',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'layanan' => 'Full Home Cleaning',
                    'alamat' => 'Jl. Anggrek No. 10, Jakarta Selatan',
                    'budget' => '35.000',
                    'biaya' => '35.000',
                    'status' => 'menunggu_mitra',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'layanan' => 'Full Home Cleaning',
            'alamat' => 'Jl. Anggrek No. 10, Jakarta Selatan',
            'nominal' => 35000,
            'biaya' => '35.000',
            'gender' => 'Bebas',
            'status' => 'menunggu_mitra',
            'step' => 4,
        ]);
    }

    public function test_order_nominal_under_10k_is_adjusted_to_minimum_10k(): void
    {
        $response = $this->postJson(route('api.orders.store'), [
            'layanan' => 'Basic Daily Cleaning',
            'alamat' => 'Jl. Mawar No. 4, Bandung',
            'budget' => '5.000',
            'tanggal' => date('Y-m-d'),
            'customer_name' => 'Budi',
            'customer_phone' => '081211112222',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('orders', [
            'layanan' => 'Basic Daily Cleaning',
            'nominal' => 10000,
            'biaya' => '10.000',
            'gender' => 'Bebas',
        ]);
    }

    public function test_customer_can_input_custom_search_duration(): void
    {
        $response = $this->postJson(route('api.orders.store'), [
            'layanan' => 'Full Home Cleaning',
            'alamat' => 'Jl. Tebet Raya No. 12',
            'budget' => '45.000',
            'tanggal' => date('Y-m-d'),
            'durasi_cari' => '30 Menit',
            'durasi_menit' => 30,
            'customer_name' => 'Siti',
            'customer_phone' => '081234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'durasi_cari' => '30 Menit',
                    'durasi_menit' => 30,
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'layanan' => 'Full Home Cleaning',
            'durasi_cari' => '30 Menit',
            'durasi_menit' => 30,
        ]);
    }

    public function test_customer_order_defaults_to_60_minutes_duration_when_not_specified(): void
    {
        $response = $this->postJson(route('api.orders.store'), [
            'layanan' => 'Basic Daily Cleaning',
            'alamat' => 'Jl. Kebon Jeruk No. 5',
            'budget' => '30.000',
            'tanggal' => date('Y-m-d'),
            'customer_name' => 'Ahmad',
            'customer_phone' => '081298765432',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'layanan' => 'Basic Daily Cleaning',
            'durasi_cari' => '1 Jam',
            'durasi_menit' => 60,
        ]);
    }
}

