<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RedeemableThresholdReachedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $threshold,
        public int $currentBalance
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Redeemable threshold reached',
            'message' => "Your points balance is now {$this->currentBalance}, which reached the redeemable threshold of {$this->threshold}.",
            'threshold' => $this->threshold,
            'current_balance' => $this->currentBalance,
        ];
    }
}
