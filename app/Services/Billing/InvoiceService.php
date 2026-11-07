<?php

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Support\Tenancy;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function createFromBooking(Booking $booking): Invoice
    {
        return DB::transaction(function () use ($booking) {
            $number = $this->nextNumber($booking->tenant_id);

            $invoice = Invoice::query()->create([
                'tenant_id' => $booking->tenant_id,
                'customer_id' => $booking->customer_id,
                'booking_id' => $booking->id,
                'number' => $number,
                'status' => InvoiceStatus::Unpaid,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'subtotal' => $booking->subtotal,
                'tax_total' => $booking->tax_total,
                'discount_total' => $booking->discount_total,
                'total' => $booking->total,
                'amount_paid' => 0,
                'balance_due' => $booking->total,
                'currency' => Tenancy::tenant()?->currency ?? 'USD',
            ]);

            foreach ($booking->items as $item) {
                InvoiceItem::query()->create([
                    'tenant_id' => $booking->tenant_id,
                    'invoice_id' => $invoice->id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_rate' => 0,
                    'line_total' => $item->line_total,
                ]);
            }

            return $invoice->load('items');
        });
    }

    public function createFromContract(Contract $contract, ?float $amount = null): Invoice
    {
        return DB::transaction(function () use ($contract, $amount) {
            $total = $amount ?? (float) $contract->contract_value;
            $number = $this->nextNumber($contract->tenant_id);

            $invoice = Invoice::query()->create([
                'tenant_id' => $contract->tenant_id,
                'customer_id' => $contract->customer_id,
                'contract_id' => $contract->id,
                'number' => $number,
                'status' => InvoiceStatus::Unpaid,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $total,
                'tax_total' => 0,
                'discount_total' => 0,
                'total' => $total,
                'amount_paid' => 0,
                'balance_due' => $total,
                'currency' => Tenancy::tenant()?->currency ?? 'USD',
                'notes' => 'Contract invoice for '.$contract->title,
            ]);

            InvoiceItem::query()->create([
                'tenant_id' => $contract->tenant_id,
                'invoice_id' => $invoice->id,
                'description' => $contract->title,
                'quantity' => 1,
                'unit_price' => $total,
                'tax_rate' => 0,
                'line_total' => $total,
            ]);

            return $invoice->load('items');
        });
    }

    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            if (in_array($invoice->status, [InvoiceStatus::Cancelled, InvoiceStatus::Refunded], true)) {
                throw new RuntimeException('Cannot record payment for a cancelled or refunded invoice.');
            }

            $amount = (float) $data['amount'];
            if ($amount <= 0) {
                throw new RuntimeException('Payment amount must be positive.');
            }

            $status = ($data['verified'] ?? false) || ($data['method'] ?? '') === 'manual'
                ? PaymentStatus::Completed
                : PaymentStatus::Pending;

            // Never mark gateway payments complete based solely on browser redirect.
            if (! empty($data['gateway']) && empty($data['gateway_verified'])) {
                $status = PaymentStatus::Pending;
            }

            $payment = Payment::query()->create([
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'reference' => $data['reference'] ?? null,
                'method' => $data['method'] ?? 'manual',
                'gateway' => $data['gateway'] ?? null,
                'gateway_transaction_id' => $data['gateway_transaction_id'] ?? null,
                'status' => $status,
                'amount' => $amount,
                'currency' => $invoice->currency,
                'paid_at' => $status === PaymentStatus::Completed ? now() : null,
                'gateway_payload' => $data['gateway_payload'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            if ($status === PaymentStatus::Completed) {
                $this->refreshInvoiceBalances($invoice->fresh());
            }

            return $payment;
        });
    }

    public function markPaymentVerified(Payment $payment, array $gatewayPayload = []): Payment
    {
        return DB::transaction(function () use ($payment, $gatewayPayload) {
            $payment->update([
                'status' => PaymentStatus::Completed,
                'paid_at' => now(),
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], $gatewayPayload),
            ]);

            $this->refreshInvoiceBalances($payment->invoice);

            return $payment->fresh();
        });
    }

    public function refreshInvoiceBalances(Invoice $invoice): Invoice
    {
        $paid = (float) $invoice->payments()
            ->where('status', PaymentStatus::Completed->value)
            ->sum('amount');

        $balance = max(0, (float) $invoice->total - $paid);
        $status = match (true) {
            $paid <= 0 && $invoice->due_date && $invoice->due_date->isPast() => InvoiceStatus::Overdue,
            $paid <= 0 => InvoiceStatus::Unpaid,
            $balance > 0 => InvoiceStatus::PartiallyPaid,
            default => InvoiceStatus::Paid,
        };

        $invoice->update([
            'amount_paid' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ]);

        return $invoice->fresh();
    }

    protected function nextNumber(int $tenantId): string
    {
        $count = Invoice::withoutGlobalScopes()->where('tenant_id', $tenantId)->count() + 1;

        return 'INV-'.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }
}
