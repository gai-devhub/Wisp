<x-mail::message>
# Account Settings Updated

Hello {{ $user->username ?? 'User' }},

We wanted to let you know that changes have been made to your WISP account settings.

### Changes Made:
@foreach($changes as $change)
* {{ $change }}
@endforeach

If you made these changes, you can safely ignore this email.

If you did not authorize these changes, please log in and update your security settings immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
