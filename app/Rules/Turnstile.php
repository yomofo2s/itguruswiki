<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Cloudflare Turnstile check. Does nothing until TURNSTILE_SECRET_KEY is set, so
 * forms keep working without it. If Cloudflare cannot be reached the check is
 * skipped (and logged) rather than locking real people out.
 */
class Turnstile implements ValidationRule
{
    /** Run even when the field is missing, so leaving the token out is not a bypass. */
    public bool $implicit = true;

    public static function enabled(): bool
    {
        return filled(config('services.turnstile.secret')) && filled(config('services.turnstile.site_key'));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::enabled()) {
            return;
        }

        try {
            $result = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => (string) $value,
                'remoteip' => request()->ip(),
            ]);
        } catch (Throwable $e) {
            report($e);

            return;
        }

        if (! $result->successful()) {
            report(new \RuntimeException('Turnstile verification returned HTTP '.$result->status()));

            return;
        }

        if (! $result->json('success')) {
            $fail('Please confirm you are not a robot and try again.');
        }
    }
}
