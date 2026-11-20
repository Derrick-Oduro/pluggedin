<?php

return [
    'upload_limit_per_month' => (int) env('MARKETPLACE_UPLOAD_LIMIT_PER_MONTH', 5),
    'referral_points_per_purchase' => (int) env('MARKETPLACE_REFERRAL_POINTS_PER_PURCHASE', 10),
    'redeemable_points_threshold' => (int) env('MARKETPLACE_REDEEMABLE_POINTS_THRESHOLD', 100),

    // Let buyers pay in cash or Mobile Money when the order is delivered or collected.
    'cash_on_delivery' => (bool) env('MARKETPLACE_CASH_ON_DELIVERY', true),
    // How many unpaid pay-on-delivery orders one buyer may have open at once (stops stock being tied up).
    'cash_on_delivery_open_limit' => (int) env('MARKETPLACE_CASH_ON_DELIVERY_OPEN_LIMIT', 2),
];
