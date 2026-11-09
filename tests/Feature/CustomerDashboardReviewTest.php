<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_an_empty_review_state_without_aggregate_ratings(): void
    {
        $response = $this->get(route('customer.dashboard'));

        $response->assertSee('Belum Ada Ulasan')
            ->assertSee('0 ulasan tercatat')
            ->assertDontSee('99.4%')
            ->assertDontSee('1.248 ulasan selesai');
    }

    public function test_dashboard_rating_and_review_feed_use_saved_order_reviews(): void
    {
        Order::factory()->create([
            'review_rating' => 4,
            'review_comment' => 'Pekerjaan rapi dan tepat waktu.',
            'reviewed_at' => now(),
        ]);
        Order::factory()->create([
            'review_rating' => 2,
            'review_comment' => 'Ulasan lain dari pesanan selesai.',
            'reviewed_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('customer.dashboard'));

        $response->assertSee('3.0')
            ->assertSee('2 ulasan tercatat')
            ->assertSee('Pekerjaan rapi dan tepat waktu.')
            ->assertSee('Ulasan lain dari pesanan selesai.');
    }
}
