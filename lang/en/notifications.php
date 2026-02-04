<?php

return [
    'booking_confirmation_subject' => 'Booking Confirmation - :number',
    'booking_confirmation_body' => "Dear :guest_name,\n\nYour booking :booking_number is confirmed.\nCheck-in: :check_in\nCheck-out: :check_out\nRoom: :room\n\nThank you for choosing us.",
    'checkout_summary_subject' => 'Check-out Summary - :number',
    'checkout_summary_body' => "Dear :guest_name,\n\nThank you for staying with us. Booking :booking_number, Room :room - check-out completed.\n\nWe hope to see you again.",
    'payment_reminder_subject' => 'Payment Reminder',
    'payment_reminder_body' => 'Dear :guest_name, this is a reminder that payment is pending for booking :booking_number.',
];
