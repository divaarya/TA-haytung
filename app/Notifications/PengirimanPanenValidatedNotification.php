<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengirimanPanenValidatedNotification extends Notification
{
    use Queueable;

    protected $pengiriman;

    public function __construct($pengiriman)
    {
        $this->pengiriman = $pengiriman;
    }

    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Kiriman panen ' . $this->pengiriman->status,
            'pengiriman_id' => $this->pengiriman->id,
        ];
    }

    public function toFcm($notifiable)
    {
        $disetujui = $this->pengiriman->status === 'disetujui';

        return [
            'title' => $disetujui ? 'Kiriman Panen Disetujui' : 'Ada Selisih Penerimaan',
            'body' => $disetujui
                ? "Gudang telah menerima {$this->pengiriman->jumlah_diterima} ekor sesuai dengan kiriman Anda."
                : "Gudang menerima {$this->pengiriman->jumlah_diterima} ekor dari {$this->pengiriman->jumlah_dikirim} ekor yang Anda kirim."
                    . ($this->pengiriman->keterangan ? ' ' . $this->pengiriman->keterangan : ''),
            'data' => [
                'type' => 'pengiriman_panen',
                'pengiriman_id' => $this->pengiriman->id,
                'status' => $this->pengiriman->status,
            ],
        ];
    }
}
