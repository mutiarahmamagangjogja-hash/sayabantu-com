<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Services\AdminRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminRealtimeController extends Controller
{
    protected AdminRealtimeService $realtimeService;

    public function __construct(AdminRealtimeService $realtimeService)
    {
        $this->realtimeService = $realtimeService;
    }

    /**
     * Endpoint snapshot state realtime untuk Admin
     */
    public function getState()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->realtimeService->getState(),
        ]);
    }

    /**
     * Server-Sent Events (SSE) Stream untuk pembaruan realtime
     */
    public function stream(Request $request): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            // Hindari buffering
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Kirim snapshot pertama kali
            $initialState = $this->realtimeService->getState();
            echo "event: initial_state\n";
            echo 'data: '.json_encode($initialState)."\n\n";
            flush();

            // Pada PHP CLI development server (php artisan serve di Windows/Local),
            // loop sleep() akan memblokir worker thread sehingga request halaman lain macet/hang.
            // Di lingkungan local / cli-server, cukup kirim snapshot awal dan heartbeat lalu selesai;
            // client JS akan melanjutkan via smart polling non-blocking.
            if (PHP_SAPI === 'cli-server' || app()->isLocal()) {
                echo ": heartbeat\n\n";
                flush();

                return;
            }

            $lastKnownUpdate = $initialState['last_updated'] ?? time();
            $loops = 0;

            // Loop streaming (maksimal 25 detik agar kompatibel dengan timeout web server)
            while ($loops < 25) {
                if (connection_aborted()) {
                    break;
                }

                $currentState = $this->realtimeService->getState();
                $currentUpdate = $currentState['last_updated'] ?? time();

                if ($currentUpdate > $lastKnownUpdate) {
                    $lastKnownUpdate = $currentUpdate;
                    echo "event: state_updated\n";
                    echo 'data: '.json_encode($currentState)."\n\n";
                    flush();
                } else {
                    // Kirim heartbeat setiap 3 detik
                    echo ": heartbeat\n\n";
                    flush();
                }

                sleep(2);
                $loops += 2;
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

    /**
     * Update status pesanan live
     */
    public function updateOrderStatus(Request $request, string $id)
    {
        $status = $request->input('status', 'Diproses');
        $state = $this->realtimeService->updateOrderStatus($id, $status);

        return response()->json([
            'status' => 'success',
            'message' => "Status pesanan {$id} berhasil diubah menjadi {$status}",
            'data' => $state,
        ]);
    }

    /**
     * Tandai notifikasi telah dibaca
     */
    public function markNotificationsRead()
    {
        $state = $this->realtimeService->markNotificationsRead();

        return response()->json([
            'status' => 'success',
            'data' => $state,
        ]);
    }

    private function formatOrderCode(string $id): string
    {
        $clean = ltrim($id, '#');
        if (str_starts_with($clean, 'SB')) {
            return '#'.$clean;
        }

        return '#SB'.$clean;
    }

    /**
     * Konfirmasi pesanan oleh admin
     */
    public function confirmOrder(Request $request, ?string $id = null)
    {
        $id = $id ?: ($request->input('id') ?: ($request->input('order_id') ?: ($request->input('code') ?: '1024')));
        $result = $this->realtimeService->confirmOrder((string) $id);
        $code = $this->formatOrderCode((string) $id);

        return response()->json([
            'status' => 'success',
            'message' => "Pesanan {$code} berhasil dikonfirmasi",
            'data' => $result['order'],
            'state' => $result['state'],
        ]);
    }

    public function confirmPayment(Request $request, string $id)
    {
        $cleanCode = ltrim($id, '#');
        $order = Order::query()
            ->where('code', $id)
            ->orWhere('code', '#'.$cleanCode)
            ->orWhere('code', $cleanCode)
            ->first();

        if ($order === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if (in_array($order->status, ['cancelled_by_admin', 'cancelled_by_customer', 'dibatalkan'], true)
            || $order->escrow_status === 'refunded') {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembayaran pesanan yang dibatalkan tidak dapat dikonfirmasi.',
            ], 409);
        }

        if ($order->payment_submitted_at === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer belum melaporkan pembayaran.',
            ], 409);
        }

        if ($order->payment_verified_at === null) {
            $order->update([
                'status' => 'terjadwal',
                'step' => 8,
                'escrow_status' => 'held',
                'escrow_hold_reason' => 'Menunggu Customer dan Admin mengonfirmasi pekerjaan selesai',
                'payment_verified_at' => now(),
            ]);

            try {
                ChatMessage::create([
                    'order_code' => $cleanCode,
                    'sender_type' => 'system',
                    'sender_name' => 'Sistem SayaBantu',
                    'message' => 'Admin telah mengonfirmasi pembayaran via '.($order->payment_method ?: 'metode tidak tercatat').'. Dana tercatat di saldo Admin dan pesanan siap diproses.',
                    'message_type' => 'payment_verified',
                ]);
            } catch (\Throwable $exception) {
                Log::warning('Could not record admin payment verification message.', [
                    'order_code' => $order->code,
                    'exception' => $exception,
                ]);
            }

            $state = $this->realtimeService->recordAdminPaymentVerification($order->fresh());
        } else {
            $state = $this->realtimeService->getState();
        }

        return response()->json([
            'status' => 'success',
            'message' => "Pembayaran {$order->code} berhasil dikonfirmasi Admin.",
            'data' => $order->fresh(),
            'state' => $state,
        ]);
    }

    /**
     * Konfirmasi akhir penyelesaian dan penutupan pesanan oleh Admin (Tahap 11)
     */
    public function confirmCompletion(Request $request, ?string $id = null)
    {
        $id = $id ?: ($request->input('id') ?: ($request->input('order_id') ?: ($request->input('code') ?: '1024')));
        $result = $this->realtimeService->confirmCompletion((string) $id);
        if (! ($result['success'] ?? false)) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Pesanan belum memenuhi syarat untuk dikonfirmasi selesai.',
            ], $result['http_status'] ?? 409);
        }

        $code = $this->formatOrderCode((string) $id);

        return response()->json([
            'status' => 'success',
            'message' => "Pesanan {$code} dikonfirmasi selesai oleh Admin. Dana tetap ditahan sampai diteruskan ke Mitra.",
            'data' => $result['order'],
            'state' => $result['state'],
        ]);
    }

    /**
     * Tandai pesanan sebagai konflik
     */
    public function markConflict(Request $request, ?string $id = null)
    {
        $id = $id ?: ($request->input('id') ?: ($request->input('order_id') ?: ($request->input('code') ?: '1024')));
        $reason = (string) $request->input('reason', 'Ditandai konflik oleh Admin');
        $result = $this->realtimeService->markOrderConflict((string) $id, $reason);
        $code = $this->formatOrderCode((string) $id);

        return response()->json([
            'status' => 'success',
            'message' => "Pesanan {$code} ditandai sebagai konflik",
            'data' => $result['order'],
            'state' => $result['state'],
        ]);
    }

    /**
     * Selesaikan konflik pesanan
     */
    public function resolveConflict(Request $request, ?string $id = null)
    {
        $id = $id ?: ($request->input('id') ?: ($request->input('order_id') ?: ($request->input('code') ?: '1024')));
        $result = $this->realtimeService->resolveOrderConflict((string) $id);
        $code = $this->formatOrderCode((string) $id);

        return response()->json([
            'status' => 'success',
            'message' => "Konflik pesanan {$code} berhasil diselesaikan",
            'data' => $result['order'],
            'state' => $result['state'],
        ]);
    }

    /**
     * Simpan catatan pesanan
     */
    public function saveNotes(Request $request, ?string $id = null)
    {
        $id = $id ?: ($request->input('id') ?: ($request->input('order_id') ?: ($request->input('code') ?: '1024')));
        $notes = (string) $request->input('notes', '');
        $result = $this->realtimeService->saveOrderNotes((string) $id, $notes);

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan pesanan berhasil disimpan',
            'data' => $result['order'],
            'state' => $result['state'],
        ]);
    }

    /**
     * Batalkan konfirmasi pesanan (kembali ke status Diproses belum dikonfirmasi)
     */
    public function unconfirmOrder(Request $request, ?string $id = null)
    {
        $id = $id ?: ($request->input('id') ?: ($request->input('order_id') ?: ($request->input('code') ?: '1024')));
        $result = $this->realtimeService->unconfirmOrder((string) $id);
        $code = $this->formatOrderCode((string) $id);

        return response()->json([
            'status' => 'success',
            'message' => "Konfirmasi pesanan {$code} dibatalkan",
            'data' => $result['order'],
            'state' => $result['state'],
        ]);
    }
}
