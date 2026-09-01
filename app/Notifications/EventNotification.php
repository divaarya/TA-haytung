<?php

namespace App\Notifications;

use App\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification
{
    use Queueable;

    protected $event;

    //  constructor nerima event
    public function __construct($event)
    {
        $this->event = $event;
    }

    // channel notifikasi
    public function via($notifiable)
    {
        return ['database', FcmChannel::class];
    }

    // isi notifikasi
    public function toArray($notifiable)
    {
        return [
            'message' => 'Event baru: ' . $this->event->nama_kegiatan,
            'tanggal' => $this->event->tanggal
        ];
    }

    // isi push FCM
    public function toFcm($notifiable)
    {
        return [
            'title' => 'Event baru: ' . $this->event->nama_kegiatan,
            'body' => $this->event->deskripsi
                ?: 'Tanggal: ' . $this->event->tanggal,
            'data' => [
                'type' => 'event',
                'event_id' => $this->event->id,
            ],
        ];
    }
}