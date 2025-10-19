<x-mail::message>
# Hello {{ $user->name }},

@if ($type == 2)
    Please verify your email by clicking the button below.
@elseif($type == 3)
    {!! '<span class="w-100">We received a request to reset your password. Please click the button below to set a new password.</span>' !!}
@endif

@component('mail::button', ['url' => url('/verify-email?token=' . $token)])
@if ($type == 2) Verify Email @elseif($type == 3) Reset Password @endif
@endcomponent
OR Submit the OTP: {{ $otp }} <br/>
If you didn't initiate this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
