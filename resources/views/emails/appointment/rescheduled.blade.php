<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Rescheduled</title>
</head>

<body style="margin:0; padding:0; background-color:#f4eddb; font-family:Arial,Helvetica,sans-serif; color:#2f2420;">

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

        $websiteName = $websiteSettings?->website_name ?? 'Padayon Massage Center';

        $websiteSubname =
            $websiteSettings?->website_subname ?? 'Blind Massage Specialists | Powered by DiverseCare Wellness Hub';
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f4eddb; padding:30px 12px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:14px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#849753; padding:30px 24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:25px; line-height:1.4;">
                                {{ $websiteName }}
                            </h1>

                            <p style="margin:8px 0 0; font-size:13px; line-height:1.7;">
                                {{ $websiteSubname }}
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px 28px;">

                            <h2 style="margin:0 0 18px; color:#6F4E37; font-size:22px;">
                                Appointment Rescheduled
                            </h2>

                            <p style="font-size:15px; line-height:1.8; color:#555555;">
                                Hello {{ $notifiable->name ?? 'there' }},
                            </p>

                            <p style="font-size:15px; line-height:1.8; color:#555555;">
                                Your appointment with {{ $websiteName }} has been
                                rescheduled successfully. Please review your updated
                                appointment details below.
                            </p>

                            <!-- Appointment Details -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin:24px 0; border:1px solid #e8e1d4; border-radius:8px;">

                                <tr>
                                    <td colspan="2"
                                        style="background-color:#f4eddb; padding:14px 16px; color:#6F4E37; font-weight:bold;">
                                        Appointment Details
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; color:#777777;">
                                        Reference
                                    </td>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; font-weight:bold;">
                                        #{{ $reference }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; color:#777777;">
                                        Service
                                    </td>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee;">
                                        {{ $appointment->service?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; color:#777777;">
                                        Therapist
                                    </td>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee;">
                                        {{ $appointment->therapist?->name ?? 'N/A' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; color:#777777;">
                                        New Date
                                    </td>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; font-weight:bold;">
                                        {{ $appointmentDate }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; color:#777777;">
                                        New Time
                                    </td>
                                    <td style="padding:12px 16px; border-bottom:1px solid #eeeeee; font-weight:bold;">
                                        {{ $appointmentTime }}
                                        @if ($appointmentEndTime)
                                            – {{ $appointmentEndTime }}
                                        @endif
                                    </td>
                                </tr>

                                @if ($appointment->addOn)
                                    <tr>
                                        <td style="padding:12px 16px; color:#777777;">
                                            Add-on
                                        </td>
                                        <td style="padding:12px 16px;">
                                            {{ $appointment->addOn->name }}
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <p style="font-size:14px; line-height:1.8; color:#555555;">
                                Please arrive on time for your updated schedule. If you
                                have questions, contact our team before your appointment.
                            </p>

                            <p style="font-size:14px; line-height:1.8; color:#555555;">
                                Thank you for choosing {{ $websiteName }}.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center"
                            style="background-color:#f4eddb; padding:20px 24px; border-top:1px solid #e4dccb;">
                            <p style="margin:0; font-size:12px; line-height:1.7; color:#6F4E37;">
                                &copy; {{ date('Y') }} {{ $websiteName }}.
                                All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
