<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InvoiceAndPaymentSeeder extends Seeder
{
    /**
     * Create fake invoices and payments for checked_out bookings.
     * Spread received_by across users; mix payment dates (today, this month) for dashboard revenue.
     */
    public function run(): void
    {
        $bookings = Booking::where('status', Booking::STATUS_CHECKED_OUT)->with(['guest', 'room'])->get();
        $users = User::all();
        if ($bookings->isEmpty() || $users->isEmpty()) {
            return;
        }

        $userIds = $users->pluck('id')->toArray();
        $today = Carbon::today();
        $paySeq = (int) Payment::withTrashed()->whereDate('created_at', $today)->count();

        foreach ($bookings as $booking) {
            $invoiceNumber = 'INV' . $booking->check_out_date->format('Ymd') . '-' . $booking->id;
            $existing = Invoice::where('booking_id', $booking->id)->first();
            if ($existing) {
                continue;
            }

            $subtotal = (float) $booking->room_rate + (float) $booking->late_checkout_fee;
            $taxRate = 15.0;
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $totalAmount = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'booking_id' => $booking->id,
                'guest_id' => $booking->guest_id,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_due' => $totalAmount,
                'status' => Invoice::STATUS_ISSUED,
                'invoice_date' => $booking->checked_out_at?->toDateString() ?? $booking->check_out_date->toDateString(),
                'due_date' => $booking->check_out_date->copy()->addDays(7)->toDateString(),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Room charge - ' . $booking->room->number,
                'item_type' => 'room',
                'quantity' => 1,
                'unit_price' => $booking->room_rate,
                'amount' => (float) $booking->room_rate,
                'bookable_id' => $booking->id,
                'bookable_type' => Booking::class,
            ]);
            if ((float) $booking->late_checkout_fee > 0) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Late checkout fee',
                    'item_type' => 'late_checkout',
                    'quantity' => 1,
                    'unit_price' => $booking->late_checkout_fee,
                    'amount' => (float) $booking->late_checkout_fee,
                ]);
            }

            // Pay full or partial; random payment date (some today, some this month) so every user sees revenue
            $payFull = fake()->boolean(85);
            $amount = $payFull ? $totalAmount : round($totalAmount * (fake()->randomFloat(2, 0.5, 0.9)), 2);
            $payDate = fake()->randomElement([
                $today->copy(),
                $today->copy()->subDays(rand(1, 15)),
                $today->copy()->subDays(rand(1, 28)),
            ]);
            $paySeq++;
            $paymentNumber = 'PAY' . $payDate->format('Ymd') . '-' . $booking->id . '-' . $paySeq;

            Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'booking_id' => $booking->id,
                'amount' => $amount,
                'method' => fake()->randomElement([Payment::METHOD_CASH, Payment::METHOD_CARD, Payment::METHOD_MOBILE_BANKING]),
                'reference' => fake()->optional(0.5)->numerify('TXN####'),
                'status' => Payment::STATUS_COMPLETED,
                'payment_date' => $payDate->copy()->setTime(rand(9, 18), 0),
                'received_by' => $userIds[array_rand($userIds)],
            ]);

            $invoice->update([
                'paid_amount' => $amount,
                'balance_due' => $totalAmount - $amount,
                'status' => $amount >= $totalAmount ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIALLY_PAID,
            ]);
        }
    }
}
