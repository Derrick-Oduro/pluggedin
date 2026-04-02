<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public int $itemsToReview
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_reminder',
            'title' => 'Share your product feedback',
            'message' => "Your order #{$this->orderId} is complete. You have {$this->itemsToReview} item(s) ready for review.",
            'order_id' => $this->orderId,
        ];
    }
}
