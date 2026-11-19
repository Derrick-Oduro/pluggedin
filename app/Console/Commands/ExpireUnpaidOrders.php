<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\PaystackPayments;
use Illuminate\Console\Command;

/**
 * Stock is reserved when an order is placed, before payment. If the buyer never
 * finishes paying, this releases that stock so the item can be sold again.
 *
 * Paystack is asked first, so a buyer who paid late is confirmed, not cancelled.
 */
class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid {--minutes=60 : Cancel pending orders older than this}';

    protected $description = 'Cancel unpaid pending orders and return their stock';

    public function handle(PaystackPayments $paystack): int
    {
        $cutoff = now()->subMinutes(max(15, (int) $this->option('minutes')));
        $cancelled = $confirmed = $skipped = 0;

        Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->with('paymentTransactions')
            ->orderBy('id')
            ->each(function (Order $order) use ($paystack, &$cancelled, &$confirmed, &$skipped) {
                foreach ($order->paymentTransactions->where('status', '!=', 'paid') as $payment) {
                    $data = $paystack->lookup($payment->reference);

                    if ($data === null && filled(config('services.paystack.secret_key'))) {
                        // Couldn't reach Paystack: don't risk cancelling a paid order. Try next run.
                        $skipped++;

                        return;
                    }

                    if (($data['status'] ?? null) === 'success') {
                        $paystack->complete($payment->reference, $data);
                        $confirmed++;

                        return;
                    }
                }

                if ($order->cancelAndRestoreInventory(null, 'Payment not completed in time.')) {
                    $cancelled++;
                }
            });

        $this->info("Expired unpaid orders: {$cancelled} cancelled, {$confirmed} confirmed late, {$skipped} skipped.");

        return self::SUCCESS;
    }
}
