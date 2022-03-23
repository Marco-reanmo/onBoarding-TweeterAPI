@component('mail::message')
Hello **{{$name}}**,  {{-- use double space for line break --}}
Thank you for signing up on **Tweeter**.

Click below to **verify** your account

<form method="POST" action={{ $link }}>
@method('PUT')
    <input
        type="submit"
        value="Verify"
    >
</form>

## Have fun.

Sincerly,
Marco
@endcomponent
