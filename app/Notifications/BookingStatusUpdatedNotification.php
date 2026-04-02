<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $bookingId,
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
        return [
            'type' => 'booking_status',
            'title' => 'Booking status updated',
            'message' => "Booking #{$this->bookingId} changed from ".ucfirst($this->fromStatus)." to ".ucfirst($this->toStatus).'.',
            'booking_id' => $this->bookingId,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
        ];
    }
}
