{{ __('Verify your Nexora email') }}

{{ __('Use this 6-digit code to verify your email address:') }} {{ $code }}

{{ __('This verification code expires in :count minutes.', ['count' => $expires]) }}