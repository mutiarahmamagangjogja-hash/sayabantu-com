<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_code' => 'SB-SURUH-'.fake()->numberBetween(1000, 9999),
            'sender_type' => fake()->randomElement(['customer', 'mitra']),
            'sender_name' => fake()->name(),
            'sender_avatar' => 'SB',
            'message' => fake()->sentence(),
            'message_type' => 'text',
            'metadata' => null,
            'is_read' => fake()->boolean(),
        ];
    }
}
