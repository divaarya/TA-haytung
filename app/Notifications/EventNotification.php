<?php

namespace App\Notifications;

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
        return ['database'];
    }

    // isi notifikasi
    public function toArray($notifiable)
    {
        return [
            'message' => 'Event baru: ' . $this->event->nama_kegiatan,
            'tanggal' => $this->event->tanggal
        ];
    }
}