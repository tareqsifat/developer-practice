<x-mail::message>
# Hello {{ $user->name }},

Please verify your email by clicking the button below.

@component('mail::button', ['url' => url('/verify-email?token=' . $token)])
Verify Email
@endcomponent
OR Submit the OTP: {{ $otp }} <br/>
If you didn't initiate this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
