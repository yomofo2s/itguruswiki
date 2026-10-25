{{-- Cloudflare Turnstile challenge; renders nothing until TURNSTILE_SITE_KEY is set. --}}
@if (\App\Rules\Turnstile::enabled())
    <div>
        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>
        @error('cf-turnstile-response')<p class="field-error">{{ $message }}</p>@enderror
        @once
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endonce
    </div>
@endif
