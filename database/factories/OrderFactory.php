<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => '#SB-SURUH-'.fake()->unique()->numberBetween(1000, 9999),
            'customer_name' => fake()->name(),
            'customer_phone' => '08'.fake()->numerify('##########'),
            'layanan' => fake()->randomElement(['Bersihkan Rumah (Full Home Cleaning)', 'Servis AC & Cuci AC', 'Jasa Angkut & Pindahan', 'Montir Panggilan']),
            'jadwal' => 'Hari ini • 13:00 WIB',
            'alamat' => fake()->address(),
            'note' => fake()->sentence(),
            'biaya' => '85.000',
            'nominal' => 85000,
            'metode' => 'Nego',
            'gender' => 'Bebas',
            'status' => 'menunggu_mitra',
            'step' => 4,
        ];
    }
}
