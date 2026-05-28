<x-emails.layout subject="Reset your password — Taktio">

    {{-- Heading --}}
    <h1 style="margin:0 0 8px;font-size:22px;font-weight:700;color:#09090b;letter-spacing:-0.3px;">
        Reset your password
    </h1>
    <p style="margin:0 0 28px;font-size:14px;color:#71717a;line-height:1.5;">
        We received a request to reset the password for your account.
    </p>

    {{-- Body --}}
    <p style="margin:0 0 28px;font-size:15px;color:#3f3f46;line-height:1.7;">
        Click the button below to choose a new password. This link is only valid for 60 minutes.
    </p>

    {{-- CTA --}}
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
        <tr>
            <td style="border-radius:8px;background-color:#09a6e9;">
                <a href="{{ $url }}"
                   target="_blank"
                   style="display:inline-block;padding:14px 28px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;letter-spacing:0.1px;">
                    Reset Password
                </a>
            </td>
        </tr>
    </table>

    {{-- Fallback link --}}
    <p style="margin:0 0 6px;font-size:13px;color:#a1a1aa;line-height:1.5;">
        If the button doesn't work, copy and paste this link into your browser:
    </p>
    <p style="margin:0 0 28px;font-size:12px;line-height:1.5;word-break:break-all;">
        <a href="{{ $url }}" style="color:#09a6e9;text-decoration:none;">{{ $url }}</a>
    </p>

    {{-- Divider --}}
    <div style="border-top:1px solid #e4e4e7;margin-bottom:24px;"></div>

    {{-- Security note --}}
    <p style="margin:0;font-size:13px;color:#a1a1aa;line-height:1.6;">
        This link will expire in <strong style="color:#71717a;">60 minutes</strong>.
        If you didn't request a password reset, no further action is required —
        your password will remain unchanged.
    </p>

</x-emails.layout>
