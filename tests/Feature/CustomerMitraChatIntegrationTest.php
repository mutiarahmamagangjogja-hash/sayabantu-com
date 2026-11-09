<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerMitraChatIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ChatMessage::where('order_code', 'SB-INTEG-CHAT-01')->delete();
        Order::where('code', '#SB-INTEG-CHAT-01')->delete();
        parent::tearDown();
    }

    public function test_chat_room_initializes_with_greeting_message(): void
    {
        $order = Order::create([
            'code' => '#SB-INTEG-CHAT-01',
            'customer_name' => 'Adelia Customer',
            'customer_phone' => '08123456789',
            'layanan' => 'Full Home Cleaning',
            'jadwal' => 'Hari ini, 15.00 WIB',
            'alamat' => 'Jl. Kebon Jeruk No. 8',
            'biaya' => '90.000',
            'nominal' => 90000,
            'mitra_name' => 'Budi Santoso',
            'mitra_phone' => '08129876543',
            'status' => 'nego',
            'step' => 5,
        ]);

        $response = $this->getJson('/api/chat/SB-INTEG-CHAT-01/messages');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order_code' => 'SB-INTEG-CHAT-01',
            ]);

        $messages = $response->json('messages');
        $this->assertNotEmpty($messages);
        $this->assertEquals('mitra', $messages[0]['sender']);
    }

    public function test_customer_can_send_message_and_mitra_receives_it(): void
    {
        $order = Order::create([
            'code' => '#SB-INTEG-CHAT-01',
            'customer_name' => 'Adelia Customer',
            'customer_phone' => '08123456789',
            'layanan' => 'Full Home Cleaning',
            'jadwal' => 'Hari ini, 15.00 WIB',
            'alamat' => 'Jl. Kebon Jeruk No. 8',
            'biaya' => '90.000',
            'nominal' => 90000,
            'mitra_name' => 'Budi Santoso',
            'mitra_phone' => '08129876543',
            'status' => 'nego',
            'step' => 5,
        ]);

        // Customer sends text message
        $sendResponse = $this->postJson('/api/chat/SB-INTEG-CHAT-01/messages', [
            'sender' => 'customer',
            'sender_name' => 'Adelia Customer',
            'text' => 'Halo Mas Budi, bisa minta tolong fokus bersihkan area kamar mandi?',
            'type' => 'text',
        ]);

        $sendResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // Mitra reads the messages
        $getRes = $this->getJson('/api/chat/SB-INTEG-CHAT-01/messages');
        $getRes->assertStatus(200);

        $messages = $getRes->json('messages');
        $last = end($messages);
        $this->assertEquals('customer', $last['sender']);
        $this->assertEquals('Halo Mas Budi, bisa minta tolong fokus bersihkan area kamar mandi?', $last['text']);
    }

    public function test_customer_and_mitra_can_negotiate_and_agree_price(): void
    {
        $order = Order::create([
            'code' => '#SB-INTEG-CHAT-01',
            'customer_name' => 'Adelia Customer',
            'customer_phone' => '08123456789',
            'layanan' => 'Full Home Cleaning',
            'jadwal' => 'Hari ini, 15.00 WIB',
            'alamat' => 'Jl. Kebon Jeruk No. 8',
            'biaya' => '100.000',
            'nominal' => 100000,
            'mitra_name' => 'Budi Santoso',
            'mitra_phone' => '08129876543',
            'status' => 'nego',
            'step' => 5,
        ]);

        // Customer submits new negotiation price: 85.000
        $negoResponse = $this->postJson('/api/chat/SB-INTEG-CHAT-01/messages', [
            'sender' => 'customer',
            'sender_name' => 'Adelia Customer',
            'text' => 'Bagaimana kalau harganya disepakati Rp 85.000?',
            'type' => 'nego',
            'metadata' => ['nominal' => '85.000'],
        ]);

        $negoResponse->assertStatus(200);

        $order->refresh();
        $this->assertEquals(85000, $order->nominal);

        // Mitra accepts the deal
        $dealResponse = $this->postJson('/api/chat/SB-INTEG-CHAT-01/messages', [
            'sender' => 'mitra',
            'sender_name' => 'Budi Santoso',
            'text' => 'Baik Mbak Adelia, tawaran Rp 85.000 saya setujui ya! Silakan lanjut ke pembayaran.',
            'type' => 'deal_accepted',
            'metadata' => ['nominal' => '85.000'],
        ]);

        $dealResponse->assertStatus(200);

        $order->refresh();
        $this->assertEquals('menunggu_konfirmasi_admin', $order->status);
    }

    public function test_admin_monitoring_shows_customer_and_mitra_messages_from_database(): void
    {
        Order::create([
            'code' => '#SB-INTEG-CHAT-ADMIN',
            'customer_name' => 'Adelia Customer',
            'customer_phone' => '08123456789',
            'layanan' => 'Full Home Cleaning',
            'jadwal' => 'Hari ini, 15.00 WIB',
            'alamat' => 'Jl. Kebon Jeruk No. 8',
            'biaya' => '90.000',
            'nominal' => 90000,
            'mitra_name' => 'Budi Santoso',
            'mitra_phone' => '08129876543',
            'status' => 'berjalan',
            'step' => 6,
        ]);
        ChatMessage::create([
            'order_code' => 'SB-INTEG-CHAT-ADMIN',
            'sender_type' => 'customer',
            'sender_name' => 'Adelia Customer',
            'message' => 'Tolong fokus ke area kamar mandi.',
            'message_type' => 'text',
        ]);
        ChatMessage::create([
            'order_code' => 'SB-INTEG-CHAT-ADMIN',
            'sender_type' => 'mitra',
            'sender_name' => 'Budi Santoso',
            'message' => 'Baik, saya sedang mengerjakannya.',
            'message_type' => 'text',
        ]);

        $response = $this->withSession(['admin_logged_in' => true])->get(route('admin.chat'));

        $response->assertOk()
            ->assertSee('Adelia Customer')
            ->assertSee('Budi Santoso')
            ->assertSee('Tolong fokus ke area kamar mandi.')
            ->assertSee('Baik, saya sedang mengerjakannya.')
            ->assertViewHas('conversationCount', 1)
            ->assertViewHas('customerMessageCount', 1)
            ->assertViewHas('mitraMessageCount', 1);

        $conversation = $response->viewData('conversations')->first();
        $this->assertSame([
            'Tolong fokus ke area kamar mandi.',
            'Baik, saya sedang mengerjakannya.',
        ], array_column($conversation['messages'], 'text'));
    }
}
