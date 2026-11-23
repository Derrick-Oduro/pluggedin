<?php

namespace App\Support;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\Shop;
use App\Models\TradeInRequest;

/**
 * Counts of work waiting for a super admin. Shown as sidebar badges and on the dashboard,
 * so each number links to the page where that work is done.
 */
class SuperAdminQueues
{
    public static function counts(): array
    {
        return once(fn () => [
            'listing_approvals' => Product::where('is_user_uploaded', true)->where('status', 'pending')->count(),
            'reports' => ProductReport::where('status', 'open')->count(),
            'trade_ins_to_quote' => TradeInRequest::whereIn('status', ['submitted', 'under_review'])->count(),
            'trade_ins_to_complete' => TradeInRequest::where('status', 'accepted')->count(),
            'shops' => Shop::where('status', 'pending')->count(),
            'messages' => ContactMessage::whereNull('handled_at')->count(),
            'cash_to_collect' => Order::where('payment_method', Order::CASH_ON_DELIVERY)->whereNull('paid_at')->whereNotIn('status', ['cancelled', 'refunded'])->count(),
        ]);
    }
}
