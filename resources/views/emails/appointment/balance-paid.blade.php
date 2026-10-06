<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Final E-Receipt</title>
</head>

<body
    style="
    margin: 0;
    padding: 0;
    background-color: #F4EDDB;
    font-family: Arial, Helvetica, sans-serif;
    color: #2F2420;
">

    @php
        $reference = str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);

        $servicePrice = (float) ($appointment->service_price ?? 0);
        $addonPrice = (float) ($appointment->addons_price ?? 0);

        $totalAmount = $servicePrice + $addonPrice;

        $previousAmountPaid = (float) ($previousAmountPaid ?? 0);
        $additionalPayment = (float) ($additionalPayment ?? 0);

        $totalPaid = (float) ($appointment->amount_paid ?? 0);

        $remainingBalance = max(0, $totalAmount - $totalPaid);

        $paymentDate = $appointment->paid_at
            ? $appointment->paid_at->format('F d, Y h:i A')
            : now('Asia/Manila')->format('F d, Y h:i A');

        $appointmentDate = optional($appointment->appointment_date)->format('F d, Y');

        $appointmentTime = $appointment->appointment_time
            ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
            : 'N/A';

        $paymentMethod = match ($appointment->payment_method) {
            'gcash' => 'GCash',
            'branch' => 'Pay at Counter',
            default => ucfirst(str_replace('_', ' ', $appointment->payment_method ?? 'N/A')),
        };
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #F4EDDB; padding: 30px 15px;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="
                    max-width: 650px;
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                ">

                    <!-- Header -->
                    <tr>
                        <td
                            style="
                            background-color: #849753;
                            padding: 30px;
                            text-align: center;
                        ">

                            <div
                                style="
                                font-size: 28px;
                                font-weight: bold;
                                color: #ffffff;
                            ">
                                Padayon Massage Center
                            </div>

                            <div
                                style="
                                margin-top: 8px;
                                font-size: 14px;
                                color: #F4EDDB;
                            ">
                                Blind Massage Specialists
                            </div>

                            <div
                                style="
                                margin-top: 3px;
                                font-size: 12px;
                                color: #F4EDDB;
                            ">
                                Powered by DiverseCare Wellness Hub
                            </div>

                        </td>
                    </tr>

                    <!-- Success Message -->
                    <tr>
                        <td style="padding: 35px 35px 15px 35px;">

                            <div
                                style="
                                text-align: center;
                                font-size: 26px;
                                font-weight: bold;
                                color: #2F2420;
                            ">
                                Remaining Balance Paid
                            </div>

                            <div
                                style="
                                margin-top: 12px;
                                text-align: center;
                                font-size: 15px;
                                line-height: 1.6;
                                color: #555555;
                            ">
                                Hello {{ $notifiable->name ?? 'Customer' }},
                                your remaining appointment balance has been
                                successfully paid.
                            </div>

                        </td>
                    </tr>

                    <!-- Final Paid Badge -->
                    <tr>
                        <td style="padding: 15px 35px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td
                                        style="
                                        background-color: #eef4e5;
                                        border: 1px solid #849753;
                                        border-radius: 8px;
                                        padding: 18px;
                                        text-align: center;
                                    ">

                                        <div
                                            style="
                                            font-size: 13px;
                                            color: #6F4E37;
                                            font-weight: bold;
                                            text-transform: uppercase;
                                            letter-spacing: 1px;
                                        ">
                                            Payment Status
                                        </div>

                                        <div
                                            style="
                                            margin-top: 6px;
                                            font-size: 24px;
                                            font-weight: bold;
                                            color: #849753;
                                        ">
                                            FULLY PAID
                                        </div>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Reference -->
                    <tr>
                        <td style="padding: 10px 35px 20px 35px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>

                                    <td
                                        style="
                                        font-size: 13px;
                                        color: #777777;
                                    ">
                                        E-Receipt Reference
                                    </td>

                                    <td align="right"
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                        color: #2F2420;
                                    ">
                                        #{{ $reference }}
                                    </td>

                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Appointment Details -->
                    <tr>
                        <td style="padding: 0 35px 20px 35px;">

                            <div
                                style="
                                font-size: 18px;
                                font-weight: bold;
                                color: #2F2420;
                                margin-bottom: 12px;
                            ">
                                Appointment Details
                            </div>

                            <table width="100%" cellpadding="8" cellspacing="0" border="0"
                                style="
                                border: 1px solid #e5dfd0;
                                border-radius: 8px;
                            ">

                                <tr>
                                    <td width="40%"
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Service
                                    </td>

                                    <td
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointment->service?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Therapist
                                    </td>

                                    <td
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointment->therapist?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Date
                                    </td>

                                    <td style="font-size: 14px;">
                                        {{ $appointmentDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Time
                                    </td>

                                    <td style="font-size: 14px;">
                                        {{ $appointmentTime }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Add-on
                                    </td>

                                    <td style="font-size: 14px;">
                                        {{ $appointment->addOn?->name ?? 'None' }}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- Payment Details -->
                    <tr>
                        <td style="padding: 0 35px 20px 35px;">

                            <div
                                style="
                                font-size: 18px;
                                font-weight: bold;
                                color: #2F2420;
                                margin-bottom: 12px;
                            ">
                                Payment Details
                            </div>

                            <table width="100%" cellpadding="8" cellspacing="0" border="0"
                                style="
                                border: 1px solid #e5dfd0;
                                border-radius: 8px;
                            ">

                                <tr>
                                    <td width="50%"
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Payment Method
                                    </td>

                                    <td
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">
                                        {{ $paymentMethod }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Payment Type
                                    </td>

                                    <td
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">
                                        Full Payment
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                        color: #777777;
                                        font-size: 14px;
                                    ">
                                        Payment Date
                                    </td>

                                    <td style="font-size: 14px;">
                                        {{ $paymentDate }}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- Payment Summary -->
                    <tr>
                        <td style="padding: 0 35px 25px 35px;">

                            <div
                                style="
                                font-size: 18px;
                                font-weight: bold;
                                color: #2F2420;
                                margin-bottom: 12px;
                            ">
                                Payment Summary
                            </div>

                            <table width="100%" cellpadding="8" cellspacing="0" border="0">

                                <!-- Service -->
                                <tr>
                                    <td style="font-size: 14px;">
                                        Service
                                    </td>

                                    <td align="right" style="font-size: 14px;">
                                        ₱{{ number_format($servicePrice, 2) }}
                                    </td>
                                </tr>

                                <!-- Add-on -->
                                @if ($addonPrice > 0)
                                    <tr>
                                        <td style="font-size: 14px;">
                                            Add-on
                                        </td>

                                        <td align="right" style="font-size: 14px;">
                                            ₱{{ number_format($addonPrice, 2) }}
                                        </td>
                                    </tr>
                                @endif

                                <!-- Total -->
                                <tr>
                                    <td
                                        style="
                                        padding-top: 12px;
                                        border-top: 1px solid #e5dfd0;
                                        font-size: 15px;
                                        font-weight: bold;
                                    ">
                                        Total Appointment Amount
                                    </td>

                                    <td align="right"
                                        style="
                                        padding-top: 12px;
                                        border-top: 1px solid #e5dfd0;
                                        font-size: 15px;
                                        font-weight: bold;
                                    ">
                                        ₱{{ number_format($totalAmount, 2) }}
                                    </td>
                                </tr>

                                <!-- Previous Payment -->
                                <tr>
                                    <td
                                        style="
                                        font-size: 14px;
                                        color: #777777;
                                    ">
                                        Previous Downpayment
                                    </td>

                                    <td align="right"
                                        style="
                                        font-size: 14px;
                                        color: #777777;
                                    ">
                                        ₱{{ number_format($previousAmountPaid, 2) }}
                                    </td>
                                </tr>

                                <!-- Additional Payment -->
                                <tr>
                                    <td
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                        color: #6F4E37;
                                    ">
                                        Remaining Balance Paid
                                    </td>

                                    <td align="right"
                                        style="
                                        font-size: 14px;
                                        font-weight: bold;
                                        color: #6F4E37;
                                    ">
                                        ₱{{ number_format($additionalPayment, 2) }}
                                    </td>
                                </tr>

                                <!-- Total Paid -->
                                <tr>
                                    <td
                                        style="
                                        padding-top: 12px;
                                        font-size: 16px;
                                        font-weight: bold;
                                        color: #2F2420;
                                    ">
                                        Total Paid
                                    </td>

                                    <td align="right"
                                        style="
                                        padding-top: 12px;
                                        font-size: 16px;
                                        font-weight: bold;
                                        color: #2F2420;
                                    ">
                                        ₱{{ number_format($totalPaid, 2) }}
                                    </td>
                                </tr>

                                <!-- Remaining -->
                                <tr>
                                    <td
                                        style="
                                        padding-top: 12px;
                                        border-top: 2px solid #849753;
                                        font-size: 17px;
                                        font-weight: bold;
                                        color: #849753;
                                    ">
                                        Remaining Balance
                                    </td>

                                    <td align="right"
                                        style="
                                        padding-top: 12px;
                                        border-top: 2px solid #849753;
                                        font-size: 17px;
                                        font-weight: bold;
                                        color: #849753;
                                    ">
                                        ₱{{ number_format($remainingBalance, 2) }}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- PayMongo Details -->
                    @if ($appointment->paymongo_reference_number || $appointment->paymongo_payment_id)
                        <tr>
                            <td style="padding: 0 35px 25px 35px;">

                                <div
                                    style="
                                    font-size: 18px;
                                    font-weight: bold;
                                    color: #2F2420;
                                    margin-bottom: 12px;
                                ">
                                    Payment Reference
                                </div>

                                <table width="100%" cellpadding="8" cellspacing="0" border="0"
                                    style="
                                    border: 1px solid #e5dfd0;
                                    border-radius: 8px;
                                ">

                                    @if ($appointment->paymongo_reference_number)
                                        <tr>
                                            <td width="45%"
                                                style="
                                                color: #777777;
                                                font-size: 13px;
                                            ">
                                                PayMongo Reference
                                            </td>

                                            <td
                                                style="
                                                font-size: 13px;
                                                font-weight: bold;
                                                word-break: break-all;
                                            ">
                                                {{ $appointment->paymongo_reference_number }}
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($appointment->paymongo_payment_id)
                                        <tr>
                                            <td
                                                style="
                                                color: #777777;
                                                font-size: 13px;
                                            ">
                                                Payment ID
                                            </td>

                                            <td
                                                style="
                                                font-size: 13px;
                                                word-break: break-all;
                                            ">
                                                {{ $appointment->paymongo_payment_id }}
                                            </td>
                                        </tr>
                                    @endif

                                </table>

                            </td>
                        </tr>
                    @endif

                    <!-- Final Message -->
                    <tr>
                        <td style="padding: 5px 35px 35px 35px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td
                                        style="
                                        background-color: #F4EDDB;
                                        border-radius: 8px;
                                        padding: 20px;
                                        text-align: center;
                                    ">

                                        <div
                                            style="
                                            font-size: 15px;
                                            font-weight: bold;
                                            color: #2F2420;
                                        ">
                                            Your appointment is now fully paid.
                                        </div>

                                        <div
                                            style="
                                            margin-top: 7px;
                                            font-size: 13px;
                                            line-height: 1.6;
                                            color: #6F4E37;
                                        ">
                                            Thank you for completing your payment
                                            with Padayon Massage Center.
                                        </div>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="
                            background-color: #2F2420;
                            padding: 25px 35px;
                            text-align: center;
                        ">

                            <div
                                style="
                                font-size: 13px;
                                color: #F4EDDB;
                                line-height: 1.6;
                            ">
                                Padayon Massage Center
                            </div>

                            <div
                                style="
                                margin-top: 5px;
                                font-size: 11px;
                                color: #cfc6b8;
                                line-height: 1.6;
                            ">
                                Blind Massage Specialists
                                <br>
                                Powered by DiverseCare Wellness Hub
                            </div>

                            <div
                                style="
                                margin-top: 12px;
                                font-size: 11px;
                                color: #aaa095;
                            ">
                                This is an electronically generated receipt.
                            </div>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
