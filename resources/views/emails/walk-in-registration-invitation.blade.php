<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Create Your Padayon Account
    </title>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        background: #f4eddb;
        font-family: Arial, Helvetica, sans-serif;
        color: #2f2420;
    ">

    <div style="
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
        ">

        <div
            style="
                background: #ffffff;
                border-radius: 16px;
                padding: 35px;
                box-shadow: 0 5px 20px rgba(0,0,0,.08);
            ">

            {{-- Header --}}
            <div style="text-align: center;">

                <h1
                    style="
                        margin: 0;
                        color: #6f4e37;
                        font-size: 26px;
                    ">
                    Padayon Massage Center
                </h1>

                <p
                    style="
                        margin-top: 8px;
                        color: #849753;
                        font-size: 14px;
                        font-weight: bold;
                    ">
                    Blind Massage Specialists
                </p>

            </div>


            {{-- Content --}}
            <div style="margin-top: 30px;">

                <h2
                    style="
                        font-size: 20px;
                        color: #2f2420;
                    ">
                    Hello {{ $walkInCustomer->name }},
                </h2>

                <p
                    style="
                        line-height: 1.7;
                        color: #555;
                    ">
                    Thank you for choosing Padayon Massage Center.
                </p>

                <p
                    style="
                        line-height: 1.7;
                        color: #555;
                    ">
                    We have recorded your walk-in appointment.
                    You can now create a Padayon account to connect
                    your appointment history to your account.
                </p>


                {{-- Button --}}
                <div
                    style="
                        text-align: center;
                        margin: 35px 0;
                    ">

                    <a href="{{ $registrationUrl }}"
                        style="
                            display: inline-block;
                            padding: 14px 28px;
                            background: #849753;
                            color: #ffffff;
                            text-decoration: none;
                            border-radius: 10px;
                            font-weight: bold;
                        ">
                        Create My Account
                    </a>

                </div>


                <p
                    style="
                        font-size: 13px;
                        line-height: 1.6;
                        color: #777;
                    ">
                    This registration invitation will expire
                    after 24 hours.
                </p>

                <p
                    style="
                        font-size: 13px;
                        line-height: 1.6;
                        color: #777;
                    ">
                    If you did not expect this message, you may
                    safely ignore this email.
                </p>

            </div>


            <hr
                style="
                    border: 0;
                    border-top: 1px solid #eee;
                    margin: 30px 0;
                ">


            <p
                style="
                    text-align: center;
                    font-size: 12px;
                    color: #999;
                ">
                Padayon Massage Center
            </p>

        </div>

    </div>

</body>

</html>
