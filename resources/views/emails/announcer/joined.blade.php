<x-mail::message>
# Новый докладчик присоединился к конференции

Добрый день, на конференцию [{{ $conferenceTitle }}]({{ config('app.url') }}/conferences/{{ $conferenceId }}) присоединился  новый участник {{ $announcerName }} с докладом на тему [{{ $lectureTitle }}]({{ config('app.url') }}/conferences/{{ $conferenceId }}/lectures/{{ $lectureId }}).

Время доклада: {{ $lectureStartTime }} - {{ $lectureEndTime }}
</x-mail::message>
