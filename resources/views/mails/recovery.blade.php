@component('mail::message')
Hello **{{$name}}**,  {{-- use double space for line break --}}
Your **Password** has been **reset**.

Your newly generated Password is: {{$newPassword}}

Please consider changing it as soon as possible.

Click below this <a href="{{$link}}">link</a> to return to the login page.

## Have fun.

Sincerly,
Marco
@endcomponent
