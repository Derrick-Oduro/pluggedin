<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PointsEarnedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $points,
        public string $reason
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Points earned',
            'message' => "You earned {$this->points} points. {$this->reason}",
            'points' => $this->points,
        ];
    }
}
