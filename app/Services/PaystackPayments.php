<?php

namespace App\Services;

use App\Models\DiscountCampaign;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\PaymentTransaction;
use App\Models\PointsTransaction;
use App\Notifications\PointsEarnedNotification;
use App\Notifications\RedeemableThresholdReachedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies Paystack transactions and applies a successful payment to its order.
 * Used by the payment callback, the webhook, and the unpaid-order expiry job.
 */
class PaystackPayments
{
    /**
     * Ask Paystack for the transaction. Returns its "data" payload, or null when
     * Paystack could not be reached or does not know the reference.
     */
    public function lookup(string $reference): ?array
    {
        try {
            $response = Http::withToken(config('services.paystack.secret_key'))
                ->acceptJson()
                ->timeout(15)
                ->get(rtrim(config('services.paystack.base_url'), '/').'/transaction/verify/'.urlencode($reference));
        } catch (\Throwable $e) {
            Log::warning('Paystack verify request failed', ['reference' => $reference, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $response->successful() || ! $response->json('status')) {
            return null;
        }

        return $response->json('data');
    }

    /** Verify with Paystack and complete the order. Aborts with 422 if the payment isn't successful. */
    public function verifyAndComplete(string $reference): void
    {
        $data = $this->lookup($reference);

        abort_unless($data && ($data['status'] ?? null) === 'success', 422, 'Payment could not be verified.');

        $this->complete($reference, $data);
    }

    /** Apply an already-verified successful Paystack payload. Safe to call more than once. */
    public function complete(string $reference, array $data): void
    {
        DB::transaction(function () use ($reference, $data) {
            $payment = PaymentTransaction::where('reference', $reference)->lockForUpdate()->firstOrFail();

            if ($payment->status === 'paid') {
                return;
            }

            $expectedAmount = (int) round(((float) $payment->amount) * 100);
            abort_unless((int) ($data['amount'] ?? 0) === $expectedAmount, 422, 'Payment amount mismatch.');

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_response' => $data,
            ]);

            $order = $payment->order()->lockForUpdate()->firstOrFail();

            if ($order->status === 'cancelled') {
                // Money arrived for an order that was already cancelled (and its stock released).
                // Needs a manual refund or re-fulfilment, so make it loud.
                Log::error('Paystack payment received for a cancelled order; refund or fulfil manually.', [
                    'order_id' => $order->id,
                    'reference' => $reference,
                    'amount' => $payment->amount,
                ]);

                return;
            }

            if ($order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'changed_by' => null,
                    'from_status' => 'pending',
                    'to_status' => 'confirmed',
                ]);

                if ($order->discount_campaign_id) {
                    DiscountCampaign::whereKey($order->discount_campaign_id)->increment('used_count');
                }

                $this->awardReferralPoints($order);
            }
        });
    }

    private function awardReferralPoints(Order $order): void
    {
        $threshold = (int) config('marketplace.redeemable_points_threshold', 100);
        $points = (int) config('marketplace.referral_points_per_purchase', 10);

        foreach ($order->items()->with('referralLink.user')->get() as $item) {
            $referralLink = $item->referralLink;
            if (! $referralLink || $referralLink->user_id === $order->user_id) {
                continue;
            }

            $referrer = $referralLink->user;
            $previousBalance = (int) $referrer->points_balance;
            $newBalance = $previousBalance + $points;
            $referrer->update(['points_balance' => $newBalance]);
            PointsTransaction::create([
                'user_id' => $referrer->id,
                'points' => $points,
                'type' => 'referral_purchase',
                'description' => 'Referral conversion from a paid purchase.',
                'meta' => ['order_id' => $order->id, 'product_id' => $item->product_id, 'referral_link_id' => $referralLink->id],
            ]);
            $referralLink->increment('conversions');
            $referrer->notify(new PointsEarnedNotification($points, 'A purchase was completed via your referral link.'));

            if ($previousBalance < $threshold && $newBalance >= $threshold) {
                $referrer->notify(new RedeemableThresholdReachedNotification($threshold, $newBalance));
            }
        }
    }
}
