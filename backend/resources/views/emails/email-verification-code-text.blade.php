{{ __('Verify your Nexora email') }}

{{ __('Your Nexora registration was completed successfully.') }}
{{ __('Use this 6-digit code to verify your email address:') }} {{ $code }}

{{ __('This verification code expires in :count minutes.', ['count' => $expires]) }}
{{ __('Terms and Conditions') }}: {{ $termsUrl }}
{{ __('Privacy Notice') }}: {{ $privacyUrl }}
{{ __('Thank you for using Nexora. Sincerely, the Nexora team.') }}