<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMitraDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_directory_renders_approved_partners_by_city_without_pending_records(): void
    {
        Mitra::factory()->verified()->create([
            'name' => 'Mitra Jakarta Aktif',
            'nik' => '3171000000000001',
            'kota' => 'Jakarta',
            'area' => 'Jakarta Selatan',
        ]);
        Mitra::factory()->verified()->create([
            'name' => 'Mitra Bandung Aktif',
            'nik' => '3273000000000002',
            'kota' => 'Bandung',
            'area' => 'Bandung Utara',
        ]);
        Mitra::factory()->create([
            'name' => 'Mitra Menunggu Persetujuan',
            'nik' => '3171000000000003',
            'kota' => 'Jakarta',
        ]);

        $response = $this->get(route('customer.mitra'));

        $response->assertSee('Mitra Jakarta Aktif')
            ->assertSee('Mitra Bandung Aktif')
            ->assertSee('Jakarta Selatan')
            ->assertSee('Bandung Utara')
            ->assertSee('Jakarta (1)')
            ->assertSee('Bandung (1)')
            ->assertDontSee('Mitra Menunggu Persetujuan')
            ->assertDontSee('3171000000000001')
            ->assertDontSee('3273000000000002');
    }

    public function test_public_directory_filters_mitras_by_the_selected_city(): void
    {
        Mitra::factory()->verified()->create([
            'name' => 'Mitra Depok',
            'kota' => 'Depok',
        ]);
        Mitra::factory()->verified()->create([
            'name' => 'Mitra Jakarta',
            'kota' => 'Jakarta',
        ]);

        $response = $this->get(route('customer.mitra', ['kota' => 'Depok']));

        $response->assertSee('Mitra Depok')
            ->assertDontSee('Mitra Jakarta');
    }

    public function test_public_mitra_metrics_use_completed_orders_and_submitted_reviews(): void
    {
        $mitra = Mitra::factory()->verified()->create([
            'name' => 'Mitra Depok Berulasan',
            'kota' => 'Depok',
        ]);
        Order::factory()->create([
            'mitra_id' => $mitra->mitra_id,
            'mitra_name' => $mitra->name,
            'status' => 'selesai',
            'review_rating' => 4,
        ]);

        $response = $this->getJson(route('api.mitra.per-kota', ['kota' => 'Depok']));

        $response->assertOk()
            ->assertJsonPath('mitras.0.rating', '4.0')
            ->assertJsonPath('mitras.0.reviewCount', 1)
            ->assertJsonPath('mitras.0.tasks', 1);
    }

    public function test_public_mitra_without_reviews_has_no_invented_rating(): void
    {
        Mitra::factory()->verified()->create([
            'name' => 'Mitra Depok Baru',
            'kota' => 'Depok',
        ]);

        $this->getJson(route('api.mitra.per-kota', ['kota' => 'Depok']))
            ->assertOk()
            ->assertJsonPath('mitras.0.rating', null)
            ->assertJsonPath('mitras.0.reviewCount', 0)
            ->assertJsonPath('mitras.0.tasks', 0);
    }
}
