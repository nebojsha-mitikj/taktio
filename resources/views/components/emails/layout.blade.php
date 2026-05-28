<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    {{-- Logo --}}
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <span style="font-size:22px;font-weight:700;color:#09a6e9;letter-spacing:-0.5px;">Taktio</span>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background-color:#ffffff;border:1px solid #e4e4e7;border-radius:12px;padding:40px 40px 36px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="padding-top:28px;">
                            <p style="margin:0;font-size:13px;color:#71717a;line-height:1.6;">
                                &copy; {{ date('Y') }} Taktio &middot;
                                <a href="https://taktio.app" style="color:#71717a;text-decoration:none;">taktio.app</a>
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;color:#a1a1aa;">
                                If you didn't expect this email, you can safely ignore it.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
