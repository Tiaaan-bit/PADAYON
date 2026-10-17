<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
</head>

<body style="margin:0;padding:0;background-color:#f4eddb;font-family:Arial,Helvetica,sans-serif;color:#2f2420;">

    @php
        $reference = str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT);

        $appointmentDate = $appointment->appointment_date
            ? \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y')
            : 'N/A';

        $appointmentTime = $appointment->appointment_time
            ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
            : 'N/A';

        $appointmentEndTime = $appointment->appointment_end_time
            ? \Carbon\Carbon::parse($appointment->appointment_end_time)->format('h:i A')
            : null;
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color:#f4eddb;padding:30px 15px;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:680px;background-color:#ffffff;border-radius:12px;overflow:hidden;">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background-color:#849753;padding:30px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                                {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}
                            </h1>

                            <p style="margin:8px 0 0;color:#ffffff;font-size:14px;line-height:1.6;">
                                {{ $websiteSettings?->website_subname ?? 'Blind Massage Specialists | Powered by DiverseCare Wellness Hub' }}
                            </p>
                        </td>
                    </tr>

                    {{-- CONFIRMATION MESSAGE --}}
                    <tr>
                        <td style="padding:35px 25px 15px;text-align:center;">

                            <div style="color:#849753;font-size:42px;font-weight:bold;">
                                ✓
                            </div>

                            <h2 style="margin:10px 0;color:#2f2420;font-size:24px;">
                                Appointment Confirmed!
                            </h2>

                            <p style="margin:0;color:#6f4e37;font-size:14px;line-height:1.8;">
                                Hello {{ $notifiable->name ?? 'Valued Customer' }},
                                <br><br>
                                Great news! Your appointment with
                                {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}
                                has been confirmed.
                                Please review your appointment details below.
                            </p>

                        </td>
                    </tr>

                    {{-- APPOINTMENT REFERENCE --}}
                    <tr>
                        <td style="padding:20px 25px 10px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color:#f4eddb;border-radius:8px;">
                                <tr>
                                    <td style="padding:18px;">
                                        <p style="margin:0 0 5px;color:#6f4e37;font-size:12px;">
                                            APPOINTMENT REFERENCE
                                        </p>

                                        <p style="margin:0;color:#2f2420;font-size:20px;font-weight:bold;">
                                            #{{ $reference }}
                                        </p>
                                    </td>

                                    <td align="right" style="padding:18px;">
                                        <span style="display:inline-block;padding:7px 12px;background-color:#849753;color:#ffffff;border-radius:20px;font-size:12px;font-weight:bold;">
                                            CONFIRMED
                                        </span>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- APPOINTMENT DETAILS --}}
                    <tr>
                        <td style="padding:25px;">

                            <h3 style="margin:0 0 15px;color:#2f2420;font-size:17px;">
                                Your Appointment Details
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Customer
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ $notifiable->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Service
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ $appointment->service?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Therapist
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ $appointment->therapist?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Date
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ $appointmentDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Time
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ $appointmentTime }}
                                        @if ($appointmentEndTime)
                                            – {{ $appointmentEndTime }}
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Add-on
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ $appointment->addOn?->name ?? 'None' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 0;color:#6f4e37;font-size:13px;">
                                        Massage Level
                                    </td>
                                    <td align="right" style="padding:10px 0;color:#2f2420;font-size:13px;font-weight:bold;">
                                        {{ ucfirst($appointment->level ?? 'N/A') }}
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    {{-- REMINDER --}}
                    <tr>
                        <td style="padding:0 25px 30px;">

                            <div style="background-color:#faf8f2;border:1px solid #e3dcc9;border-radius:8px;padding:18px;">

                                <h3 style="margin:0 0 10px;color:#2f2420;font-size:15px;">
                                    Appointment Reminder
                                </h3>

                                <p style="margin:0;color:#6f4e37;font-size:13px;line-height:1.8;">
                                    Please arrive on time for your scheduled appointment.
                                    If you need to cancel or reschedule, kindly do so through
                                    your Padayon Massage Center account or contact our staff.
                                </p>

                            </div>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background-color:#2f2420;padding:25px;text-align:center;">

                            <p style="margin:0 0 8px;color:#ffffff;font-size:13px;font-weight:bold;">
                                {{ $websiteSettings?->website_name ?? 'Padayon Massage Center' }}
                            </p>

                            <p style="margin:0;color:#d8cfbd;font-size:11px;line-height:1.8;">
                                Thank you for choosing us.
                                <br>
                                We look forward to serving you!
                            </p>

                            <p style="margin:15px 0 0;color:#d8cfbd;font-size:10px;line-height:1.6;">
                                This is an automated appointment confirmation email.
                                Please keep it for your records.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
