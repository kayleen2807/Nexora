{{ __('You have been invited to Nexora') }}

{{ __('Hello :name.', ['name' => $user->nombre]) }}
{{ __('You were registered by :admin.', ['admin' => $invitedBy->nombre . ' ' . $invitedBy->apellido]) }}
{{ __('Branch: :branch', ['branch' => $branch]) }}
{{ __('Role: :role', ['role' => $role]) }}
{{ __('Registration date: :date', ['date' => $registrationDate?->format('Y-m-d')]) }}
{{ $url }}

{{ __('This link expires in :count hours.', ['count' => $expires]) }}

{{ __('Terms and Conditions') }}: {{ $termsUrl }}
{{ __('Privacy Notice') }}: {{ $privacyUrl }}