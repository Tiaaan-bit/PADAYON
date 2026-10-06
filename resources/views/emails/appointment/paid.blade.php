<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment Receipt</title>
</head>

<body
    style="
    margin: 0;
    padding: 0;
    background-color: #f4eddb;
    font-family: Arial, Helvetica, sans-serif;
    color: #2f2420;
">

    @php
        /*
    |--------------------------------------------------------------------------
    | Appointment Amounts
    |--------------------------------------------------------------------------
    */

        $servicePrice = (float) ($appointment->service_price ?? 0);
        $addonPrice = (float) ($appointment->addons_price ?? 0);

        $totalAmount = $servicePrice + $addonPrice;

        /*
    |--------------------------------------------------------------------------
    | Actual amount paid
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | For downpayment, this is only the downpayment amount.
    */

        $amountPaid = (float) ($appointment->amount_paid ?? 0);

        $remainingBalance = max(0, $totalAmount - $amountPaid);

        /*
    |--------------------------------------------------------------------------
    | Reference
    |--------------------------------------------------------------------------
    */

        $reference = str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);

        /*
    |--------------------------------------------------------------------------
    | Payment Method
    |--------------------------------------------------------------------------
    */

        $paymentMethod = match ($appointment->payment_method) {
            'gcash' => 'GCash',
            'branch' => 'Pay at Counter',
            default => ucfirst(str_replace('_', ' ', $appointment->payment_method ?? 'N/A')),
        };

        /*
    |--------------------------------------------------------------------------
    | Payment Type
    |--------------------------------------------------------------------------
    */

        $paymentType = match ($appointment->payment_type) {
            'full' => 'Full Payment',
            'downpayment' => 'Downpayment',
            default => ucfirst(str_replace('_', ' ', $appointment->payment_type ?? 'N/A')),
        };

        /*
    |--------------------------------------------------------------------------
    | Appointment Date / Time
    |--------------------------------------------------------------------------
    */

        $appointmentDate = $appointment->appointment_date
            ? \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y')
            : 'N/A';

        $appointmentTime = $appointment->appointment_time
            ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
            : 'N/A';

        $appointmentEndTime = $appointment->appointment_end_time
            ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A')
            : null;

        /*
    |--------------------------------------------------------------------------
    | Paid Date
    |--------------------------------------------------------------------------
    */

        $paidAt = $appointment->paid_at
            ? \Carbon\Carbon::parse($appointment->paid_at)->timezone('Asia/Manila')->format('F d, Y h:i A')
            : 'N/A';
    @endphp


    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f4eddb; padding: 30px 15px;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="
                    max-width: 680px;
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                ">

                    {{-- =====================================================
                     HEADER
                ====================================================== --}}

                    <tr>
                        <td
                            style="
                            background-color: #849753;
                            padding: 30px;
                            text-align: center;
                        ">

                            <h1
                                style="
                            margin: 0;
                            color: #ffffff;
                            font-size: 28px;
                            font-weight: bold;
                        ">
                                Padayon Massage Center
                            </h1>

                            <p
                                style="
                            margin: 8px 0 0;
                            color: #ffffff;
                            font-size: 14px;
                        ">
                                Blind Massage Specialists
                            </p>

                            <p
                                style="
                            margin: 4px 0 0;
                            color: #ffffff;
                            font-size: 12px;
                            opacity: 0.9;
                        ">
                                Powered by DiverseCare Wellness Hub
                            </p>

                        </td>
                    </tr>


                    {{-- =====================================================
                     PAYMENT SUCCESS
                ====================================================== --}}

                    <tr>
                        <td style="padding: 35px 35px 10px;">

                            <div
                                style="
                            text-align: center;
                            color: #849753;
                            font-size: 42px;
                            font-weight: bold;
                        ">
                                ✓
                            </div>

                            <h2
                                style="
                            margin: 10px 0 5px;
                            text-align: center;
                            color: #2f2420;
                            font-size: 24px;
                        ">
                                Payment Received
                            </h2>

                            <p
                                style="
                            margin: 0;
                            text-align: center;
                            color: #6f4e37;
                            font-size: 14px;
                        ">
                                Thank you. Your payment has been successfully recorded.
                            </p>

                        </td>
                    </tr>


                    {{-- =====================================================
                     RECEIPT REFERENCE
                ====================================================== --}}

                    <tr>
                        <td style="padding: 25px 35px 10px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="
                                background-color: #f4eddb;
                                border-radius: 8px;
                            ">
                                <tr>

                                    <td style="padding: 18px;">

                                        <p
                                            style="
                                        margin: 0 0 5px;
                                        color: #6f4e37;
                                        font-size: 12px;
                                    ">
                                            RECEIPT REFERENCE
                                        </p>

                                        <p
                                            style="
                                        margin: 0;
                                        color: #2f2420;
                                        font-size: 20px;
                                        font-weight: bold;
                                    ">
                                            #{{ $reference }}
                                        </p>

                                    </td>

                                    <td align="right" style="padding: 18px;">

                                        <span
                                            style="
                                        display: inline-block;
                                        padding: 7px 12px;
                                        background-color: #849753;
                                        color: #ffffff;
                                        border-radius: 20px;
                                        font-size: 12px;
                                        font-weight: bold;
                                    ">
                                            PAID
                                        </span>

                                    </td>

                                </tr>
                            </table>

                        </td>
                    </tr>


                    {{-- =====================================================
                     CUSTOMER / APPOINTMENT DETAILS
                ====================================================== --}}

                    <tr>
                        <td style="padding: 20px 35px;">

                            <h3
                                style="
                            margin: 0 0 15px;
                            color: #2f2420;
                            font-size: 17px;
                        ">
                                Appointment Details
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Customer
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $notifiable->name }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Service
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointment->service?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Therapist
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointment->therapist?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Date
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointmentDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Time
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointmentTime }}

                                        @if ($appointmentEndTime)
                                            – {{ $appointmentEndTime }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Add-on
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $appointment->addOn?->name ?? 'None' }}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>


                    {{-- =====================================================
                     PAYMENT INFORMATION
                ====================================================== --}}

                    <tr>
                        <td style="padding: 10px 35px 20px;">

                            <h3
                                style="
                            margin: 0 0 15px;
                            color: #2f2420;
                            font-size: 17px;
                        ">
                                Payment Information
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Payment Method
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $paymentMethod }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Payment Type
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $paymentType }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Payment Date
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                    ">
                                        {{ $paidAt }}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>


                    {{-- =====================================================
                     AMOUNT BREAKDOWN
                ====================================================== --}}

                    <tr>
                        <td style="padding: 10px 35px 30px;">

                            <h3
                                style="
                            margin: 0 0 15px;
                            color: #2f2420;
                            font-size: 17px;
                        ">
                                Payment Summary
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 13px;
                                ">
                                        Service
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                    ">
                                        ₱{{ number_format($servicePrice, 2) }}
                                    </td>
                                </tr>

                                @if ($addonPrice > 0)
                                    <tr>
                                        <td
                                            style="
                                        padding: 8px 0;
                                        color: #6f4e37;
                                        font-size: 13px;
                                    ">
                                            Add-on
                                        </td>

                                        <td align="right"
                                            style="
                                            padding: 8px 0;
                                            color: #2f2420;
                                            font-size: 13px;
                                        ">
                                            ₱{{ number_format($addonPrice, 2) }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td colspan="2"
                                        style="
                                        border-top: 1px solid #ddd3bd;
                                        padding-top: 12px;
                                    ">
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #2f2420;
                                    font-size: 15px;
                                    font-weight: bold;
                                ">
                                        Total Amount
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #2f2420;
                                        font-size: 15px;
                                        font-weight: bold;
                                    ">
                                        ₱{{ number_format($totalAmount, 2) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="
                                    padding: 8px 0;
                                    color: #6f4e37;
                                    font-size: 14px;
                                    font-weight: bold;
                                ">
                                        Amount Paid
                                    </td>

                                    <td align="right"
                                        style="
                                        padding: 8px 0;
                                        color: #849753;
                                        font-size: 16px;
                                        font-weight: bold;
                                    ">
                                        ₱{{ number_format($amountPaid, 2) }}
                                    </td>
                                </tr>

                                @if ($remainingBalance > 0)
                                    <tr>
                                        <td
                                            style="
                                        padding: 12px 0 8px;
                                        color: #6f4e37;
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">
                                            Remaining Balance
                                        </td>

                                        <td align="right"
                                            style="
                                            padding: 12px 0 8px;
                                            color: #c0392b;
                                            font-size: 16px;
                                            font-weight: bold;
                                        ">
                                            ₱{{ number_format($remainingBalance, 2) }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td
                                            style="
                                        padding: 12px 0 8px;
                                        color: #6f4e37;
                                        font-size: 14px;
                                        font-weight: bold;
                                    ">
                                            Remaining Balance
                                        </td>

                                        <td align="right"
                                            style="
                                            padding: 12px 0 8px;
                                            color: #849753;
                                            font-size: 16px;
                                            font-weight: bold;
                                        ">
                                            ₱0.00
                                        </td>
                                    </tr>
                                @endif

                            </table>

                        </td>
                    </tr>


                    {{-- =====================================================
                     PAYMONGO DETAILS
                ====================================================== --}}

                    @if (
                        $appointment->payment_method === 'gcash' &&
                            ($appointment->paymongo_reference_number || $appointment->paymongo_payment_id))

                        <tr>
                            <td style="padding: 0 35px 30px;">

                                <div
                                    style="
                                background-color: #faf8f2;
                                border: 1px solid #e3dcc9;
                                border-radius: 8px;
                                padding: 18px;
                            ">

                                    <h3
                                        style="
                                    margin: 0 0 12px;
                                    color: #2f2420;
                                    font-size: 15px;
                                ">
                                        Payment Reference
                                    </h3>

                                    @if ($appointment->paymongo_reference_number)
                                        <p
                                            style="
                                        margin: 6px 0;
                                        color: #6f4e37;
                                        font-size: 12px;
                                    ">
                                            PayMongo Reference
                                        </p>

                                        <p
                                            style="
                                        margin: 0 0 12px;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                        word-break: break-all;
                                    ">
                                            {{ $appointment->paymongo_reference_number }}
                                        </p>
                                    @endif

                                    @if ($appointment->paymongo_payment_id)
                                        <p
                                            style="
                                        margin: 6px 0;
                                        color: #6f4e37;
                                        font-size: 12px;
                                    ">
                                            Payment ID
                                        </p>

                                        <p
                                            style="
                                        margin: 0;
                                        color: #2f2420;
                                        font-size: 13px;
                                        font-weight: bold;
                                        word-break: break-all;
                                    ">
                                            {{ $appointment->paymongo_payment_id }}
                                        </p>
                                    @endif

                                </div>

                            </td>
                        </tr>

                    @endif


                    {{-- =====================================================
                     FOOTER
                ====================================================== --}}

                    <tr>
                        <td
                            style="
                            background-color: #2f2420;
                            padding: 25px 35px;
                            text-align: center;
                        ">

                            <p
                                style="
                            margin: 0 0 8px;
                            color: #ffffff;
                            font-size: 13px;
                            font-weight: bold;
                        ">
                                Padayon Massage Center
                            </p>

                            <p
                                style="
                            margin: 0;
                            color: #d8cfbd;
                            font-size: 11px;
                            line-height: 1.6;
                        ">
                                Thank you for choosing Padayon Massage Center.
                                <br>
                                We look forward to serving you.
                            </p>

                            <p
                                style="
                            margin: 15px 0 0;
                            color: #d8cfbd;
                            font-size: 10px;
                        ">
                                This is an automated payment receipt.
                                Please keep it for your records.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>ss
