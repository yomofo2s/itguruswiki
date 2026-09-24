<?php

namespace App\Support;

use Closure;
use Throwable;

/**
 * Sends mail without letting a mail-server problem break the page.
 * On shared hosting mail goes out synchronously (QUEUE_CONNECTION=sync), so an
 * SMTP error would otherwise turn a saved contact message into a "Server Error".
 * Failures are written to storage/logs and the caller can inform the user.
 */
class SafeMail
{
    public static function send(Closure $send): bool
    {
        try {
            $send();

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
