<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

// Channel notifikasi custom yang ngirim push lewat FCM ke fcm_token milik
// $notifiable. Dipakai bareng channel lain (mis. 'database') di method
// via() suatu Notification -- notification class yang mau kepakein channel
// ini wajib punya method toFcm($notifiable) yang balikin
// ['title' => ..., 'body' => ..., 'data' => [...]].
class FcmChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $token = $notifiable->fcm_token ?? null;

        if (empty($token) || !method_exists($notification, 'toFcm')) {
            return;
        }

        // SEMUA yang berhubungan sama Firebase (bangun pesan, resolusi
        // Messaging, kirim) dibungkus try/catch -- baik gagal karena
        // package belum ke-install, credential belum/salah disetup, atau
        // token invalid, sama sekali gak boleh ikut nggagalin aksi utama
        // (mis. bikin Event/Permintaan/Validasi Panen). Cukup dicatat log.
        try {
            $payload = $notification->toFcm($notifiable);

            $message = CloudMessage::fromArray([
                'token' => $token,
                'notification' => [
                    'title' => $payload['title'] ?? '',
                    'body' => $payload['body'] ?? '',
                ],
                'data' => array_map('strval', $payload['data'] ?? []),
            ]);

            app(Messaging::class)->send($message);
        } catch (Throwable $e) {
            Log::warning('Gagal kirim FCM push: ' . $e->getMessage(), [
                'notifiable_id' => $notifiable->id ?? null,
                'notification' => $notification::class,
            ]);
        }
    }
}
