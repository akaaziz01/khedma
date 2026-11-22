<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ActivityNotification extends Notification
{
    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
