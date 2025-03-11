<x-mail::message>
    {{-- Header --}}
    # Nieuw bericht van de website

    {{-- Contactgegevens --}}
    Naam: {{ $data['name'] }}
    Email: {{ $data['email'] }}

    {{-- Bericht --}}
    Bericht:
    <x-mail::panel>
        {{ $data['message'] }}
    </x-mail::panel>

    Bedankt,<br>
    {{ config('app.name') }}
</x-mail::message>
