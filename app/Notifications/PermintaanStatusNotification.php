<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PermintaanStatusNotification extends Notification
{
    use Queueable;

    protected $permintaan;

    public function __construct($permintaan)
    {
        $this->permintaan = $permintaan;
    }

    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Permintaan "' . $this->permintaan->nama_permintaan . '" ' . $this->permintaan->status,
            'permintaan_id' => $this->permintaan->id,
        ];
    }

    public function toFcm($notifiable)
    {
        $disetujui = $this->permintaan->status === 'disetujui';

        return [
            'title' => $disetujui ? 'Permintaan Disetujui' : 'Permintaan Ditolak',
            'body' => $disetujui
                ? $this->permintaan->nama_permintaan . ' telah disetujui.'
                : $this->permintaan->nama_permintaan . ' ditolak'
                    . ($this->permintaan->alasan_tolak ? ': ' . $this->permintaan->alasan_tolak : '.'),
            'data' => [
                'type' => 'permintaan',
                'permintaan_id' => $this->permintaan->id,
                'status' => $this->permintaan->status,
            ],
        ];
    }
}
