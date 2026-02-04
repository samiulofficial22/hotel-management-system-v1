<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public const TYPE_BOOKING_CONFIRMATION = 'booking_confirmation';
    public const TYPE_CHECK_IN = 'check_in_notification';
    public const TYPE_CHECK_OUT_SUMMARY = 'check_out_summary';
    public const TYPE_PAYMENT_REMINDER = 'payment_reminder';
    public const TYPE_MARKETING = 'marketing';

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SMS = 'sms';

    /**
     * Send email and log to notification_logs.
     */
    public function sendEmail(string $to, string $subject, string $body, string $type = self::TYPE_BOOKING_CONFIRMATION, ?string $notifiableType = null, ?int $notifiableId = null): NotificationLog
    {
        $log = NotificationLog::create([
            'type' => $type,
            'channel' => self::CHANNEL_EMAIL,
            'recipient' => $to,
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending',
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
        ]);

        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            $log->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('NotificationService sendEmail failed: ' . $e->getMessage());
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }

        return $log->fresh();
    }

    /**
     * Send SMS (stub: log only; integrate Twilio/etc. as needed).
     */
    public function sendSms(string $to, string $body, string $type = self::TYPE_BOOKING_CONFIRMATION, ?string $notifiableType = null, ?int $notifiableId = null): NotificationLog
    {
        $log = NotificationLog::create([
            'type' => $type,
            'channel' => self::CHANNEL_SMS,
            'recipient' => $to,
            'subject' => null,
            'body' => $body,
            'status' => 'pending',
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
        ]);

        try {
            // Stub: no real SMS provider; mark as sent for testing. Replace with Twilio/etc.
            if (config('services.sms.driver') === 'log') {
                Log::info('SMS (stub): to=' . $to . ' body=' . substr($body, 0, 100));
                $log->update(['status' => 'sent', 'sent_at' => now()]);
            } else {
                $log->update(['status' => 'failed', 'error_message' => 'SMS driver not configured']);
            }
        } catch (\Throwable $e) {
            Log::error('NotificationService sendSms failed: ' . $e->getMessage());
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }

        return $log->fresh();
    }

    /**
     * Send booking confirmation to guest email.
     */
    public function sendBookingConfirmation(\App\Models\Booking $booking): ?NotificationLog
    {
        $guest = $booking->guest;
        $email = $guest->email ?? null;
        if (!$email) {
            return null;
        }
        $subject = __('notifications.booking_confirmation_subject', ['number' => $booking->booking_number]);
        $body = __('notifications.booking_confirmation_body', [
            'guest_name' => $guest->full_name,
            'booking_number' => $booking->booking_number,
            'check_in' => $booking->check_in_date->format('Y-m-d'),
            'check_out' => $booking->check_out_date->format('Y-m-d'),
            'room' => $booking->room->number ?? '-',
        ]);
        return $this->sendEmail($email, $subject, $body, self::TYPE_BOOKING_CONFIRMATION, \App\Models\Booking::class, $booking->id);
    }

    /**
     * Send check-out summary to guest email.
     */
    public function sendCheckOutSummary(\App\Models\Booking $booking): ?NotificationLog
    {
        $guest = $booking->guest;
        $email = $guest->email ?? null;
        if (!$email) {
            return null;
        }
        $subject = __('notifications.checkout_summary_subject', ['number' => $booking->booking_number]);
        $body = __('notifications.checkout_summary_body', [
            'guest_name' => $guest->full_name,
            'booking_number' => $booking->booking_number,
            'room' => $booking->room->number ?? '-',
        ]);
        return $this->sendEmail($email, $subject, $body, self::TYPE_CHECK_OUT_SUMMARY, \App\Models\Booking::class, $booking->id);
    }
}
