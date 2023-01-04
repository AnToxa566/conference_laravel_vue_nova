<x-mail::message>
# Время проведения лекции было измененно

Добрый день, на конференции [{{ $conferenceTitle }}]({{ config('app.url') }}/#/conferences/{{ $conferenceId }}) участник {{ $announcerName }} с докладом на тему [{{ $lectureTitle }}]({{ config('app.url') }}/#/conferences/{{ $conferenceId }}/lectures/{{ $lectureId }}) перенес доклад на другое время.

Новое время доклада: {{ $lectureStartTime }} - {{ $lectureEndTime }}
</x-mail::message>
