<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<body style="margin:0;padding:0;background:#f6f7f3;font-family:Arial,Helvetica,sans-serif;color:#151515;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 12px;background:#f6f7f3;">
        <tr><td align="center"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#fff;border:1px solid #d7ded5;border-radius:8px;">
            <tr><td align="center" style="padding:32px 24px 16px;"><img src="{{ $logoUrl }}" alt="Nexora" width="180" style="display:block;width:180px;height:auto;"></td></tr>
            <tr><td style="padding:8px 32px 32px;">
                <h1 style="color:#174831;">{{ __('Your Nexora security code') }}</h1>
                <p>{{ $intro }}</p>
                <p style="margin:24px 0;text-align:center;font-size:32px;font-weight:bold;letter-spacing:8px;color:#174831;">{{ $code }}</p>
                <p style="color:#6f766f;">{{ __('This verification code expires in :count minutes.', ['count' => $expires]) }}</p>
                <p style="color:#6f766f;">{{ __('If you did not request this code, change your password: someone may know it.') }}</p>
                <p style="color:#6f766f;">{{ __('Thank you for using Nexora. Sincerely, the Nexora team.') }}</p>
            </td></tr>
        </table></td></tr>
    </table>
</body>
</html>
