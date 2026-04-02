<?php

return [
    'upload_limit_per_month' => (int) env('MARKETPLACE_UPLOAD_LIMIT_PER_MONTH', 5),
    'referral_points_per_purchase' => (int) env('MARKETPLACE_REFERRAL_POINTS_PER_PURCHASE', 10),
    'redeemable_points_threshold' => (int) env('MARKETPLACE_REDEEMABLE_POINTS_THRESHOLD', 100),
];
