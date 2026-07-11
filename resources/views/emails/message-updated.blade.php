<x-mail::message>
# Message Updated

Hello {{ $user->username ?? 'User' }},

We wanted to let you know that your message **"{{ $messageModel->title }}"** has been updated.

### Changes Made:
@foreach($changes as $change)
* {{ $change }}
@endforeach

<x-mail::button :url="route('user.my-messages.page')" color="primary">
View Messages
</x-mail::button>

If you did not authorize these changes, please log in and update your security settings immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
