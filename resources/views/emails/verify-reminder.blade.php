<x-mail::message>
# Welcome to WISP, {{ $user->username ?? 'User' }}!

We're so glad to have you on board. It's been 24 hours since you created your account, and we noticed you haven't verified your email address yet.

To get the most out of your experience and secure your account, please verify your email address by clicking the button below.

<x-mail::button :url="$url" color="primary">
Verify Email Address
</x-mail::button>

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
