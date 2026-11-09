<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Mengambil daftar pesan untuk kode pesanan tertentu.
     */
    public function getMessages(string $orderCode): JsonResponse
    {
        $cleanCode = ltrim(trim($orderCode), '#');
        $numCode = preg_replace('/[^0-9]/', '', $cleanCode);

        $order = Order::where('code', $cleanCode)
            ->orWhere('code', '#'.$cleanCode)
            ->when($numCode !== '', function ($q) use ($numCode) {
                $q->orWhere('id', (int) $numCode)
                    ->orWhere('code', $numCode)
                    ->orWhere('code', '#'.$numCode)
                    ->orWhere('code', 'like', '%'.$numCode);
            })
            ->first();

        $canonicalCode = $order ? ltrim($order->code, '#') : $cleanCode;

        $codesToMatch = array_values(array_unique(array_filter([
            $cleanCode,
            '#'.$cleanCode,
            $canonicalCode,
            '#'.$canonicalCode,
            $numCode !== '' ? $numCode : null,
            $numCode !== '' ? '#'.$numCode : null,
            $order ? (string) $order->id : null,
        ])));

        $role = request()->query('role');
        if ($role === 'customer') {
            ChatMessage::whereIn('order_code', $codesToMatch)
                ->where('sender_type', '!=', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } elseif ($role === 'mitra') {
            ChatMessage::whereIn('order_code', $codesToMatch)
                ->where('sender_type', '!=', 'mitra')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $messages = ChatMessage::whereIn('order_code', $codesToMatch)
            ->orderBy('id', 'asc')
            ->get();

        // Jika belum ada pesan, inisialisasi pesan pembuka otomatis dari sistem/mitra
        if ($messages->isEmpty()) {
            $mitraName = $order?->mitra_name ?: 'Mitra SayaBantu';
            $custName = $order?->customer_name ?: 'Pelanggan';
            $layanan = $order?->layanan ?: 'Full Home Cleaning';

            $initMitra = ChatMessage::create([
                'order_code' => $canonicalCode,
                'sender_type' => 'mitra',
                'sender_name' => $mitraName,
                'sender_avatar' => strtoupper(substr($mitraName, 0, 2)),
                'message' => "Halo {$custName}, saya {$mitraName} mitra SayaBantu untuk pesanan {$layanan}. Apakah ada catatan khusus terkait lokasi atau jadwal penugasan?",
                'message_type' => 'text',
                'is_read' => true,
            ]);

            $messages = collect([$initMitra]);
        }

        return response()->json([
            'success' => true,
            'order_code' => $canonicalCode,
            'order' => $order ? [
                'code' => $order->code,
                'clean_code' => $canonicalCode,
                'layanan' => $order->layanan,
                'biaya' => $order->biaya,
                'nominal' => $order->nominal,
                'status' => $order->status,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'mitra_name' => $order->mitra_name,
                'mitra_phone' => $order->mitra_phone,
            ] : null,
            'messages' => $messages->map(function (ChatMessage $msg): array {
                return [
                    'id' => $msg->id,
                    'order_code' => $msg->order_code,
                    'sender' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'sender_avatar' => $msg->sender_avatar,
                    'text' => $msg->message,
                    'type' => $msg->message_type,
                    'metadata' => $msg->metadata,
                    'time' => $msg->created_at->format('H:i'),
                    'timestamp' => $msg->created_at->timestamp,
                    'is_read' => $msg->is_read,
                ];
            }),
        ]);
    }

    /**
     * Mengirim pesan baru ke percakapan pesanan.
     */
    public function sendMessage(Request $request, string $orderCode): JsonResponse
    {
        $cleanCode = ltrim(trim($orderCode), '#');
        $numCode = preg_replace('/[^0-9]/', '', $cleanCode);

        $validated = $request->validate([
            'text' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
            'sender' => ['required', 'string', 'in:customer,mitra,admin,system'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'sender_avatar' => ['nullable', 'string', 'max:10'],
            'type' => ['nullable', 'string', 'max:50'],
            'metadata' => ['nullable'],
        ]);

        $text = trim((string) ($validated['text'] ?? ($validated['message'] ?? '')));
        if ($text === '') {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak boleh kosong.',
            ], 422);
        }

        $order = Order::where('code', $cleanCode)
            ->orWhere('code', '#'.$cleanCode)
            ->when($numCode !== '', function ($q) use ($numCode) {
                $q->orWhere('id', (int) $numCode)
                    ->orWhere('code', $numCode)
                    ->orWhere('code', '#'.$numCode)
                    ->orWhere('code', 'like', '%'.$numCode);
            })
            ->first();

        $canonicalCode = $order ? ltrim($order->code, '#') : $cleanCode;

        $senderType = $validated['sender'];
        if ($senderType === 'customer') {
            $defaultSenderName = $order?->customer_name ?: 'Pelanggan';
        } elseif ($senderType === 'mitra') {
            $defaultSenderName = $order?->mitra_name ?: 'Mitra SayaBantu';
        } elseif ($senderType === 'admin') {
            $defaultSenderName = 'Admin SayaBantu';
        } else {
            $defaultSenderName = 'Sistem SayaBantu';
        }

        $senderName = trim((string) ($validated['sender_name'] ?? $defaultSenderName));
        if ($senderName === '' || $senderName === 'Pelanggan' || $senderName === 'Mitra SayaBantu') {
            $senderName = $defaultSenderName;
        }

        $defaultSenderAvatar = match ($senderType) {
            'customer' => strtoupper(substr($order?->customer_name ?: 'Pelanggan', 0, 2)),
            'mitra' => strtoupper(substr($order?->mitra_name ?: 'Mitra SayaBantu', 0, 2)),
            'admin' => 'AD',
            default => 'SB',
        };
        $senderAvatar = trim((string) ($validated['sender_avatar'] ?? $defaultSenderAvatar));
        if ($senderAvatar === '') {
            $senderAvatar = $defaultSenderAvatar;
        }
        $messageType = trim((string) ($validated['type'] ?? 'text'));
        $metadata = is_array($validated['metadata'] ?? null) ? $validated['metadata'] : null;

        // Jika pesan bertipe nego, perbarui status dan harga pesanan di database
        if ($messageType === 'nego' && is_array($metadata) && ! empty($metadata['nominal'])) {
            $rawNominal = (string) $metadata['nominal'];
            $nominal = (int) preg_replace('/[^0-9]/', '', $rawNominal);
            if ($nominal > 0 && $order) {
                $order->update([
                    'biaya' => number_format($nominal, 0, ',', '.'),
                    'nominal' => $nominal,
                    'status' => 'nego',
                    'step' => 5,
                ]);
            }
        } elseif ($messageType === 'deal_accepted' && $order) {
            $order->update([
                'status' => 'menunggu_konfirmasi_admin',
                'step' => 6,
            ]);
        }

        $chatMsg = ChatMessage::create([
            'order_code' => $canonicalCode,
            'sender_type' => $senderType,
            'sender_name' => $senderName,
            'sender_avatar' => $senderAvatar,
            'message' => $text,
            'message_type' => $messageType,
            'metadata' => $metadata,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil terkirim.',
            'data' => [
                'id' => $chatMsg->id,
                'order_code' => $chatMsg->order_code,
                'sender' => $chatMsg->sender_type,
                'sender_name' => $chatMsg->sender_name,
                'sender_avatar' => $chatMsg->sender_avatar,
                'text' => $chatMsg->message,
                'type' => $chatMsg->message_type,
                'metadata' => $chatMsg->metadata,
                'time' => $chatMsg->created_at->format('H:i'),
                'timestamp' => $chatMsg->created_at->timestamp,
                'is_read' => $chatMsg->is_read,
            ],
            'order' => $order ? [
                'code' => $order->code,
                'clean_code' => $canonicalCode,
                'biaya' => $order->biaya,
                'nominal' => $order->nominal,
                'status' => $order->status,
            ] : null,
        ]);
    }

    /**
     * Mengambil daftar percakapan aktif untuk menu tab pesan.
     */
    public function getConversations(Request $request): JsonResponse
    {
        $role = $request->query('role', 'customer');

        $orders = Order::orderBy('updated_at', 'desc')->take(20)->get();

        $conversations = $orders->map(function (Order $order) use ($role): array {
            $cleanCode = ltrim($order->code, '#');
            $numCode = preg_replace('/[^0-9]/', '', $cleanCode);

            $codesToMatch = array_values(array_unique(array_filter([
                $cleanCode,
                '#'.$cleanCode,
                $numCode !== '' ? $numCode : null,
                $numCode !== '' ? '#'.$numCode : null,
                (string) $order->id,
            ])));

            $lastMessage = ChatMessage::whereIn('order_code', $codesToMatch)
                ->orderBy('id', 'desc')
                ->first();

            $unreadCount = ChatMessage::whereIn('order_code', $codesToMatch)
                ->where('sender_type', '!=', $role)
                ->where('is_read', false)
                ->count();

            $partnerName = ($role === 'mitra')
                ? ($order->customer_name ?: 'Pelanggan SayaBantu')
                : ($order->mitra_name ?: 'Mitra SayaBantu');

            return [
                'order_code' => $cleanCode,
                'display_code' => $order->code,
                'partner_name' => $partnerName,
                'partner_avatar' => strtoupper(substr($partnerName, 0, 2)),
                'service' => $order->layanan,
                'status' => $order->status,
                'biaya' => $order->biaya,
                'last_message' => $lastMessage?->message ?: "Halo, saya siap membantu pesanan {$order->layanan}.",
                'last_time' => $lastMessage?->created_at?->format('H:i') ?: $order->updated_at?->format('H:i') ?: 'Baru saja',
                'unread' => $unreadCount,
            ];
        });

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }
}
