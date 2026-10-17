
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin:0; padding:0; background-color:#f4eddb; font-family:Arial,Helvetica,sans-serif; color:#2f2420;">

    @php
        $websiteName = $websiteSettings?->website_name
            ?? 'Padayon Massage Center';

        $websiteSubname = $websiteSettings?->website_subname
            ?? 'Blind Massage Specialists | Powered by DiverseCare Wellness Hub';
    @endphp

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background-color:#f4eddb; padding:30px 12px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td align="center"
                            style="background-color:#849753; padding:30px 24px; color:#ffffff;">
                            <h1 style="margin:0; font-size:26px; line-height:1.4;">
                                {{ $websiteName }}
                            </h1>

                            <p style="margin:8px 0 0; font-size:13px; line-height:1.6;">
                                {{ $websiteSubname }}
                            </p>
                        </td>
                    </tr>

                    <!-- Email Content -->
                    <tr>
                        <td style="padding:32px 28px;">

                            <h2 style="margin:0 0 18px; color:#6F4E37; font-size:22px;">
                                Reset Your Password
                            </h2>

                            <p style="font-size:15px; line-height:1.8; margin:0 0 16px;">
                                Hello!                            </p>

                            <p style="font-size:15px; line-height:1.8; margin:0 0 20px;">
                                We received a request to reset the password for your
                                {{ $websiteName }} account. Click the button below to
                                choose a new password.
                            </p>

                            <!-- Reset Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0"
                                   style="margin:24px auto;">
                                <tr>
                                    <td align="center" style="background-color:#6F4E37; border-radius:8px;">
                                        <a href="{{ $url }}"
                                           style="display:inline-block; padding:15px 28px; color:#ffffff; text-decoration:none; font-size:15px; font-weight:bold; border-radius:8px;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px; line-height:1.8; margin:20px 0 12px;">
                                This password reset link will expire in
                                <strong>{{ $expireMinutes }} minutes</strong>.
                            </p>

                            <p style="font-size:14px; line-height:1.8; margin:0 0 16px;">
                                If the button does not work, copy and paste this link
                                into your browser:
                            </p>

                            <p style="font-size:13px; line-height:1.7; overflow-wrap:anywhere; word-break:break-word;">
                                <a href="{{ $url }}" style="color:#6F4E37;">
                                    {{ $url }}
                                </a>
                            </p>

                            <p style="font-size:14px; line-height:1.8; margin:24px 0 0;">
                                If you did not request a password reset, you can safely
                                ignore this email. Your password will not change unless
                                the reset process is completed.
                            </p>

                            <p style="font-size:14px; line-height:1.8; margin:24px 0 0;">
                                For your security, do not share this link with anyone.
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
