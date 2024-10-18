<x-mail::message>
Halo<br>

your request visit is {{ $action }}

@if ($action == 'accepted')
<hr>
Congratulations, below is a QR Code that you can use:<br><br>
<img src="{{ $message->embed(public_path() . '/storage/' . $filename) }}">
@endif

</x-mail::message>
