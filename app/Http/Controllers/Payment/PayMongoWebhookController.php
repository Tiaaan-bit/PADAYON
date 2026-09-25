<?php

namespace App\Http\Controllers\Payment;

use App\Enums\Admin\Appointment\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\UsersAppointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Verify PayMongo webhook signature
        |--------------------------------------------------------------------------
        */

        if (!$this->verifySignature($request)) {
            Log::warning('Invalid PayMongo webhook signature.');

            return response()->json(
                [
                    'message' => 'Invalid signature.',
                ],
                401,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Get raw payload
        |--------------------------------------------------------------------------
        */

        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            Log::warning('Invalid PayMongo webhook payload.');

            return response()->json(
                [
                    'message' => 'Invalid payload.',
                ],
                400,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Get event information
        |--------------------------------------------------------------------------
        */

        $eventType = data_get($payload, 'data.attributes.type');

        $livemode = (bool) data_get($payload, 'data.attributes.livemode', false);

        Log::info('PayMongo webhook received.', [
            'event_type' => $eventType,
            'livemode' => $livemode,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Handle failed payments
        |--------------------------------------------------------------------------
        |
        | PayMongo sends:
        |
        | payment.failed
        |
        | when a payment fails or the checkout expires.
        |
        */

        if ($eventType === 'payment.failed') {
            $payment = data_get($payload, 'data.attributes.data');

            $paymentId = data_get($payment, 'id');

            $paymentAttributes = data_get($payment, 'attributes', []);

            $description = data_get($paymentAttributes, 'description');

            $failedCode = data_get($paymentAttributes, 'failed_code');

            $failedMessage = data_get($paymentAttributes, 'failed_message');

            Log::info('PayMongo payment failed.', [
                'payment_id' => $paymentId,
                'description' => $description,
                'failed_code' => $failedCode,
                'failed_message' => $failedMessage,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Extract payment reference
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Padayon Massage Center Appointment APPT-123-A8K4P2
            |
            */

            if (!$description || !preg_match('/(APPT-\d+-[A-Z0-9]{6})/', $description, $matches)) {
                Log::warning('Unable to identify payment reference.', [
                    'payment_id' => $paymentId,
                    'description' => $description,
                ]);

                return response()->json(
                    [
                        'message' => 'Payment reference not found.',
                    ],
                    200,
                );
            }

            $paymentReference = $matches[1];

            /*
            |--------------------------------------------------------------------------
            | Extract appointment ID
            |--------------------------------------------------------------------------
            */

            if (!preg_match('/APPT-(\d+)-[A-Z0-9]{6}/', $paymentReference, $appointmentMatches)) {
                Log::warning('Unable to identify appointment from payment reference.', [
                    'payment_reference' => $paymentReference,
                ]);

                return response()->json(
                    [
                        'message' => 'Appointment reference not found.',
                    ],
                    200,
                );
            }

            $appointmentId = (int) $appointmentMatches[1];

            /*
            |--------------------------------------------------------------------------
            | Find appointment
            |--------------------------------------------------------------------------
            */

            $appointment = UsersAppointments::find($appointmentId);

            if (!$appointment) {
                Log::warning('Appointment not found for failed payment.', [
                    'appointment_id' => $appointmentId,
                    'payment_id' => $paymentId,
                ]);

                return response()->json(
                    [
                        'message' => 'Appointment not found.',
                    ],
                    200,
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Ignore stale payment attempts
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Old payment:
            | APPT-122-ABC123
            |
            | New Pay Again payment:
            | APPT-122-XYZ789
            |
            | If the old failure arrives after the retry,
            | do not mark the new payment as failed.
            |
            */

            if ($appointment->paymongo_reference_number && $appointment->paymongo_reference_number !== $paymentReference) {
                Log::warning('Stale PayMongo failed payment ignored.', [
                    'appointment_id' => $appointment->id,
                    'payment_id' => $paymentId,
                    'failed_reference' => $paymentReference,
                    'current_reference' => $appointment->paymongo_reference_number,
                ]);

                return response()->json(
                    [
                        'message' => 'Stale payment attempt ignored.',
                    ],
                    200,
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Do not overwrite successful payment
            |--------------------------------------------------------------------------
            */

            if ($appointment->payment_status === 'paid') {
                Log::info('Failed payment ignored because appointment is already paid.', [
                    'appointment_id' => $appointment->id,
                    'payment_id' => $paymentId,
                ]);

                return response()->json(
                    [
                        'message' => 'Appointment already paid.',
                    ],
                    200,
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Mark appointment as failed
            |--------------------------------------------------------------------------
            */

            $appointment->update([
                'paymongo_payment_id' => $paymentId,
                'payment_status' => 'failed',
                'status' => AppointmentStatus::FAILED,
            ]);

            Log::info('Appointment marked as failed.', [
                'appointment_id' => $appointment->id,
                'payment_id' => $paymentId,
                'failed_code' => $failedCode,
                'failed_message' => $failedMessage,
            ]);

            return response()->json(
                [
                    'message' => 'Payment failure processed successfully.',
                ],
                200,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Ignore other events
        |--------------------------------------------------------------------------
        */

        if ($eventType !== 'checkout_session.payment.paid') {
            Log::info('PayMongo event ignored.', [
                'event_type' => $eventType,
            ]);

            return response()->json(
                [
                    'message' => 'Event ignored.',
                ],
                200,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Get Checkout Session
        |--------------------------------------------------------------------------
        */

        $checkoutSession = data_get($payload, 'data.attributes.data');

        $checkoutSessionId = data_get($checkoutSession, 'id');

        $referenceNumber = data_get($checkoutSession, 'attributes.reference_number');

        if (!$checkoutSessionId || !$referenceNumber) {
            Log::warning('PayMongo webhook missing checkout information.', [
                'checkout_session_id' => $checkoutSessionId,
                'reference_number' => $referenceNumber,
            ]);

            return response()->json(
                [
                    'message' => 'Missing checkout information.',
                ],
                400,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Validate payment reference
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | APPT-123-A8K4P2
        |
        */

        if (!preg_match('/APPT-(\d+)-[A-Z0-9]{6}/', $referenceNumber, $matches)) {
            Log::warning('Invalid appointment reference.', [
                'reference_number' => $referenceNumber,
            ]);

            return response()->json(
                [
                    'message' => 'Invalid appointment reference.',
                ],
                200,
            );
        }

        $appointmentId = (int) $matches[1];

        /*
        |--------------------------------------------------------------------------
        | 8. Find Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = UsersAppointments::find($appointmentId);

        if (!$appointment) {
            Log::warning('PayMongo webhook appointment not found.', [
                'appointment_id' => $appointmentId,
                'reference_number' => $referenceNumber,
                'checkout_session_id' => $checkoutSessionId,
            ]);

            return response()->json(
                [
                    'message' => 'Appointment not found.',
                ],
                404,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Verify payment reference belongs to current attempt
        |--------------------------------------------------------------------------
        |
        | This prevents an old successful payment attempt from
        | confirming a newer Pay Again attempt.
        |
        */

        if ($appointment->paymongo_reference_number && $appointment->paymongo_reference_number !== $referenceNumber) {
            Log::warning('Stale PayMongo successful payment ignored.', [
                'appointment_id' => $appointment->id,
                'webhook_reference' => $referenceNumber,
                'current_reference' => $appointment->paymongo_reference_number,
                'checkout_session_id' => $checkoutSessionId,
            ]);

            return response()->json(
                [
                    'message' => 'Stale payment attempt ignored.',
                ],
                200,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Verify Checkout Session belongs to appointment
        |--------------------------------------------------------------------------
        */

        if ($appointment->paymongo_checkout_session_id && $appointment->paymongo_checkout_session_id !== $checkoutSessionId) {
            Log::warning('PayMongo Checkout Session mismatch.', [
                'appointment_id' => $appointment->id,
                'database_session_id' => $appointment->paymongo_checkout_session_id,
                'webhook_session_id' => $checkoutSessionId,
            ]);

            return response()->json(
                [
                    'message' => 'Checkout Session mismatch.',
                ],
                400,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Idempotency
        |--------------------------------------------------------------------------
        */

        if ($appointment->payment_status === 'paid') {
            Log::info('PayMongo payment already processed.', [
                'appointment_id' => $appointment->id,
                'checkout_session_id' => $checkoutSessionId,
            ]);

            return response()->json(
                [
                    'message' => 'Payment already processed.',
                ],
                200,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 12. Get payment
        |--------------------------------------------------------------------------
        */

        $payments = data_get($checkoutSession, 'attributes.payments', []);

        $payment = $payments[0] ?? null;

        $paymentId = data_get($payment, 'id');

        $paidAmountInSmallestUnit = data_get($payment, 'attributes.amount');

        if ($paidAmountInSmallestUnit === null) {
            Log::warning('PayMongo webhook missing payment amount.', [
                'appointment_id' => $appointment->id,
                'checkout_session_id' => $checkoutSessionId,
            ]);

            return response()->json(
                [
                    'message' => 'Missing payment amount.',
                ],
                400,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 13. Convert PayMongo amount
        |--------------------------------------------------------------------------
        */

        $paidAmount = $paidAmountInSmallestUnit / 100;

        $expectedAmount = (float) $appointment->payment_amount;

        /*
        |--------------------------------------------------------------------------
        | 14. Verify payment amount
        |--------------------------------------------------------------------------
        */

        if (abs($paidAmount - $expectedAmount) > 0.01) {
            Log::warning('PayMongo payment amount mismatch.', [
                'appointment_id' => $appointment->id,
                'expected_amount' => $expectedAmount,
                'paid_amount' => $paidAmount,
                'payment_id' => $paymentId,
            ]);

            return response()->json(
                [
                    'message' => 'Payment amount mismatch.',
                ],
                400,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 15. Update appointment
        |--------------------------------------------------------------------------
        */

        $appointment->update([
            'paymongo_checkout_session_id' => $checkoutSessionId,
            'paymongo_reference_number' => $referenceNumber,
            'paymongo_payment_id' => $paymentId,
            'amount_paid' => $paidAmount,
            'payment_status' => 'paid',
            'paid_at' => now('Asia/Manila'),
            'status' => AppointmentStatus::CONFIRMED,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 16. Notify user
        |--------------------------------------------------------------------------
        */

        $appointment->user->notify(new \App\Notifications\AppointmentConfirmedNotification($appointment));

        Log::info('PayMongo payment confirmed.', [
            'appointment_id' => $appointment->id,
            'checkout_session_id' => $checkoutSessionId,
            'payment_id' => $paymentId,
            'reference_number' => $referenceNumber,
            'amount_paid' => $paidAmount,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 17. Acknowledge PayMongo
        |--------------------------------------------------------------------------
        */

        return response()->json(
            [
                'message' => 'Payment processed successfully.',
            ],
            200,
        );
    }

    private function verifySignature(Request $request): bool
    {
        $signatureHeader = $request->header('Paymongo-Signature');

        $webhookSecret = config('services.paymongo.webhook_secret');

        if (!$signatureHeader || !$webhookSecret) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Parse:
        |
        | t=timestamp
        | te=test-signature
        | li=live-signature
        |--------------------------------------------------------------------------
        */

        $parts = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, null);

            if ($key !== null) {
                $parts[$key] = $value;
            }
        }

        $timestamp = $parts['t'] ?? null;

        $testSignature = $parts['te'] ?? null;

        $liveSignature = $parts['li'] ?? null;

        if (!$timestamp) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent replay attacks
        |--------------------------------------------------------------------------
        */

        $timestampAge = abs(time() - (int) $timestamp);

        if ($timestampAge > 300) {
            Log::warning('PayMongo webhook timestamp is too old.', [
                'timestamp' => $timestamp,
                'age' => $timestampAge,
            ]);

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate expected signature
        |--------------------------------------------------------------------------
        */

        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $request->getContent(), $webhookSecret);

        $providedSignature = $testSignature ?: $liveSignature;

        if (!$providedSignature) {
            return false;
        }

        return hash_equals($expectedSignature, $providedSignature);
    }
}
