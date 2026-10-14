<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset your password') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f7f3;font-family:Arial,Helvetica,sans-serif;color:#151515;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f6f7f3;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background-color:#ffffff;border:1px solid #d7ded5;border-radius:8px;">
                    <tr>
                        <td align="center" style="padding:32px 24px 16px;">
                            <img src="{{ $logoUrl }}" alt="Nexora" width="180" style="display:block;width:180px;max-width:100%;height:auto;border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 32px;">
                            <h1 style="margin:0 0 20px;color:#174831;font-size:24px;line-height:1.3;">{{ __('Reset your password') }}</h1>
                            <p style="margin:0 0 18px;color:#444;line-height:1.6;">{{ __('You are receiving this email because we received a password reset request for your account.') }}</p>
                            <p style="margin:0 0 24px;color:#444;line-height:1.6;">{{ __('Click the button below to choose a new password.') }}</p>
                            <p style="margin:0 0 24px;text-align:center;">
                                <a href="{{ $url }}" style="display:inline-block;padding:14px 24px;border-radius:6px;background-color:#ef8e19;color:#ffffff;font-weight:bold;text-decoration:none;">{{ __('Reset Password') }}</a>
                            </p>
                            <p style="margin:0 0 16px;color:#6f766f;font-size:14px;line-height:1.6;">{{ __('This password reset link will expire in :count minutes.', ['count' => $expires]) }}</p>
                            <p style="margin:0;color:#6f766f;font-size:14px;line-height:1.6;">{{ __('If you did not request a password reset, no further action is required.') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>