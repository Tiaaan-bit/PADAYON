
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code</title>
</head>

<body style="margin:0;padding:0;background-color:#f4eddb;font-family:Arial,Helvetica,sans-serif;color:#2f2420;">

    @php
        $websiteName = $websiteSettings?->website_name ?? 'Padayon Massage Center';

        $websiteSubname = $websiteSettings?->website_subname
            ?? 'Blind Massage Specialists | Powered by DiverseCare Wellness Hub';
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color:#f4eddb;padding:30px 15px;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background-color:#849753;padding:30px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:bold;">
                                {{ $websiteName }}
                            </h1>

                            <p style="margin:8px 0 0;color:#ffffff;font-size:14px;line-height:1.6;">
                                {{ $websiteSubname }}
                            </p>
                        </td>
                    </tr>

                    {{-- VERIFICATION MESSAGE --}}
                    <tr>
                        <td style="padding:35px 25px 15px;text-align:center;">

                            <div style="color:#849753;font-size:42px;font-weight:bold;">
                                &#10003;
                            </div>

                            <h2 style="margin:10px 0;color:#2f2420;font-size:24px;">
                                Verify Your Email Address
                            </h2>

                            <p style="margin:0;color:#6f4e37;font-size:14px;line-height:1.8;">
                                Hello {{ $notifiable->name ?? 'Valued Customer' }},
                                <br><br>
                                Thank you for registering with {{ $websiteName }}.
                                Please use the verification code below to verify
                                your email address.
                            </p>

                        </td>
                    </tr>

                    {{-- VERIFICATION CODE --}}
                    <tr>
                        <td style="padding:20px 25px 25px;">

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color:#f4eddb;border:1px solid #e3dcc9;border-radius:8px;">
                                <tr>
                                    <td align="center" style="padding:25px 15px;">

                                        <p style="margin:0 0 12px;color:#6f4e37;font-size:12px;font-weight:bold;letter-spacing:1px;">
                                            YOUR VERIFICATION CODE
                                        </p>

                                        <p style="margin:0;color:#2f2420;font-size:32px;font-weight:bold;letter-spacing:8px;">
                                            {{ $code }}
                                        </p>

                                        <p style="margin:15px 0 0;color:#b45309;font-size:13px;font-weight:bold;">
                                            Expires in 5 minutes
                                        </p>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- SECURITY REMINDER --}}
                    <tr>
                        <td style="padding:0 25px 30px;">

                            <div style="background-color:#faf8f2;border:1px solid #e3dcc9;border-radius:8px;padding:18px;">

                                <h3 style="margin:0 0 10px;color:#2f2420;font-size:15px;">
                                    Keep Your Code Safe
                                </h3>

                                <p style="margin:0;color:#6f4e37;font-size:13px;line-height:1.8;">
                                    Do not share this code with anyone.
                                    Our team will never ask you to disclose your verification code.
                                    If you did not request this code, you can safely ignore this email.
                                </p>

                            </div>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background-color:#2f2420;padding:25px;text-align:center;">

                            <p style="margin:0 0 8px;color:#ffffff;font-size:13px;font-weight:bold;">
                                {{ $websiteName }}
                            </p>

                            <p style="margin:0;color:#d8cfbd;font-size:11px;line-height:1.8;">
                                Thank you for choosing us.
                                <br>
                                We look forward to serving you!
                            </p>

                            <p style="margin:15px 0 0;color:#d8cfbd;font-size:10px;line-height:1.6;">
                                This is an automated email verification message.
                                Please do not reply to this email.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
