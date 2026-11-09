<?php

namespace Database\Factories;

use App\Models\Mitra;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mitra>
 */
class MitraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mitra_id' => 'SB-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => fake()->name(),
            'nik' => fake()->numerify('3171############'),
            'phone' => '08'.fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'kota' => fake()->randomElement(['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi']),
            'area' => 'Semua Area Kota',
            'alamat' => fake()->address(),
            'pin' => '123456',
            'keahlian' => fake()->randomElement(['Full Home Cleaning & Servis AC', 'Pindahan, Angkut Beban & Logistik', 'Tukang Ledeng & Perbaikan Rumah']),
            'service' => fake()->randomElement(['Full Home Cleaning & Servis AC', 'Pindahan, Angkut Beban & Logistik']),
            'jaminan' => 'KTP Asli',
            'avatar' => null,
            'rating' => 5.0,
            'tasks' => 0,
            'status' => 'verifikasi',
            'status_label' => 'Menunggu Verifikasi & ACC Admin',
        ];
    }

    /**
     * Indicate that the mitra is active/verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aktif',
            'status_label' => 'Aktif Terverifikasi',
        ]);
    }
}
