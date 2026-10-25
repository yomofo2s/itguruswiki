{{-- Encrypted render time; the server rejects submissions that are too fast or too old. --}}
<input type="hidden" name="started_at" value="{{ \App\Support\FormTimer::token() }}">
