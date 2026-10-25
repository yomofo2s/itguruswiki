<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

/**
 * Spam bots post a form instantly or replay a stale one; people take a few seconds.
 * The form carries an encrypted timestamp that the server checks on submit.
 */
class FormTimer
{
    public static function token(): string
    {
        return Crypt::encryptString((string) now()->timestamp);
    }

    public static function passes(?string $token, int $minSeconds = 4, int $maxSeconds = 7200): bool
    {
        $age = now()->timestamp - (int) rescue(fn () => Crypt::decryptString((string) $token), 0, false);

        return $age >= $minSeconds && $age <= $maxSeconds;
    }
}
