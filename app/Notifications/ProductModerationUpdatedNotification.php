<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductModerationUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $productId,
        public string $productName,
        public string $status,
        public ?string $adminComment = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $baseMessage = "Your upload '{$this->productName}' was ".ucfirst($this->status).'.';
        $comment = $this->adminComment ? ' Admin note: '.$this->adminComment : '';

        return [
            'type' => 'product_moderation',
            'title' => 'Product moderation update',
            'message' => $baseMessage.$comment,
            'product_id' => $this->productId,
            'status' => $this->status,
        ];
    }
}
