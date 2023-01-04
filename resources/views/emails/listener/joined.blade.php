<x-mail::message>
# Новый слушатель присоединился к конференции

Добрый день, на конференцию [{{ $conferenceTitle }}]({{ config('app.url') }}/#/conferences/{{ $conferenceId }}) присоединился  новый слушатель {{ $listenerName }}.
</x-mail::message>
