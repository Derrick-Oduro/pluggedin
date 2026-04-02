<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public string $fromStatus,
        public string $toStatus
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = fn (string $status): string => ucfirst($status);

        return [
            'type' => 'order_status',
            'title' => 'Order status updated',
            'message' => "Order #{$this->orderId} changed from {$statusLabel($this->fromStatus)} to {$statusLabel($this->toStatus)}.",
            'order_id' => $this->orderId,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
        ];
    }
}
