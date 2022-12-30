<x-mail::message>
# Новый комментарий под вашим докладом

Добрый день, на конференции [{{ $conferenceTitle }}]({{ config('app.url') }}/#/conferences/{{ $conferenceId }}) пользователь {{ $userName }} оставил комментарий на ваш доклад [{{ $lectureTitle }}]({{ config('app.url') }}/#/conferences/{{ $conferenceId }}/lectures/{{ $lectureId }}).
</x-mail::message>

