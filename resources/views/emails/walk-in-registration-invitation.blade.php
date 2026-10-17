<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Padayon Account</title>
</head>

<body style="margin:0; padding:0; background-color:#f4eddb; font-family:Arial,Helvetica,sans-serif; color:#2f2420;">

    @php
        $websiteName = $websiteSettings?->website_name ?? 'Padayon Massage Center';

        $websiteSubname =
            $websiteSettings?->website_subname ?? 'Blind Massage Specialists | Powered by DiverseCare Wellness Hub';
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f4eddb; padding:30px 12px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background-color:#ffffff; border-radius:16px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color:#849753; padding:30px 24px; color:#ffffff;">

                            <h1 style="margin:0; font-size:26px; line-height:1.4;">
                                {{ $websiteName }}
                            </h1>

                            <p style="margin:8px 0 0; font-size:13px; line-height:1.7;">
                                {{ $websiteSubname }}
                            </p>

                        </td>
                    </tr>

                    <!-- Email Content -->
                    <tr>
                        <td style="padding:32px 28px;">

                            <h2 style="margin:0 0 18px; color:#6F4E37; font-size:22px;">
                                Hello {{ $walkInCustomer->name }}!
                            </h2>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.8; color:#555555;">
                                Thank you for choosing {{ $websiteName }}.
                                We appreciate your trust in our massage services.
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.8; color:#555555;">
                                We have recorded your walk-in appointment.
                                You can create an account to connect your appointment
                                history with your Padayon account and make it easier
                                to manage your future appointments.
                            </p>

                            <!-- Registration Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px auto;">
                                <tr>
                                    <td align="center" style="background-color:#6F4E37; border-radius:8px;">
                                        <a href="{{ $registrationUrl }}"
                                            style="display:inline-block; padding:15px 28px; color:#ffffff; text-decoration:none; font-size:15px; font-weight:bold; border-radius:8px;">
                                            Create My Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 12px; font-size:14px; line-height:1.8; color:#555555;">
                                If the button does not work, copy and paste this link
                                into your browser:
                            </p>

                            <p
                                style="margin:0 0 20px; font-size:13px; line-height:1.7; overflow-wrap:anywhere; word-break:break-word;">
                                <a href="{{ $registrationUrl }}" style="color:#6F4E37;">
                                    {{ $registrationUrl }}
                                </a>
                            </p>

                            <div
                                style="background-color:#f4eddb; border-left:4px solid #849753; padding:14px 16px; margin:24px 0;">
                                <p style="margin:0; font-size:13px; line-height:1.7; color:#555555;">
                                    <strong>Important:</strong> This registration invitation
                                    is valid for 24 hours. Please create your account before
                                    the invitation expires.
                                </p>
                            </div>

                            <p style="margin:20px 0 0; font-size:14px; line-height:1.8; color:#555555;">
                                If you did not expect this message, you may safely ignore
                                this email.
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

                            <p style="margin:6px 0 0; font-size:12px; line-height:1.7; color:#777777;">
                                Thank you for choosing our services.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
