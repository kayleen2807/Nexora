{{ __('Reset your password') }}

{{ __('You are receiving this email because we received a password reset request for your account.') }}

{{ __('Click the button below to choose a new password.') }}
{{ $url }}

{{ __('This password reset link will expire in :count minutes.', ['count' => $expires]) }}

{{ __('If you did not request a password reset, no further action is required.') }}