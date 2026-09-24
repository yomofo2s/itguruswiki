<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Browser-triggered maintenance for hosting plans without SSH or cron.
 *
 * Enabled only while ITG_SETUP_TOKEN (min. 32 characters) is set in .env:
 *   https://www.itgurusgermany.com/_setup/<token>                      -> migrate + seed + storage link
 *   https://www.itgurusgermany.com/_setup/<token>?admin=you@mail.com   -> also promote that account to admin
 *
 *   ...?mailtest=1   -> send a test email and show the SMTP error, if any
 *   ...?log=1        -> show the latest error log entries
 * The ?admin= option only works while no administrator exists yet.
 * Also used by the automatic GitHub deployment (.github/workflows/ci.yml) to run migrations.
 */
class SetupController extends Controller
{
    public function __invoke(Request $request, string $token): Response
    {
        $expected = (string) config('itgurus.setup_token');

        if (strlen($expected) < 32 || ! hash_equals($expected, $token)) {
            return response("Not found\n", 404)->header('Content-Type', 'text/plain');
        }

        $log = [];
        $run = function (string $command, array $args = []) use (&$log): bool {
            try {
                $code = Artisan::call($command, $args);
                $log[] = "$ {$command}\n".trim(Artisan::output())."\n[exit {$code}]";

                return $code === 0;
            } catch (Throwable $e) {
                $log[] = "$ {$command}\nERROR: ".$e->getMessage();

                return false;
            }
        };

        $ok = $run('migrate', ['--force' => true])
            && $run('db:seed', ['--class' => 'CategorySeeder', '--force' => true]);

        if (! is_link(public_path('storage'))) {
            $run('storage:link');
        }
        $run('optimize:clear');

        if ($ok && $email = $request->query('admin')) {
            $email = Str::lower(trim($email));

            if (User::where('role', Role::Admin->value)->exists()) {
                $log[] = 'An administrator already exists - manage roles in the admin area (Users & roles) instead.';
            } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $log[] = "'{$email}' is not a valid email address.";
            } elseif ($user = User::where('email', $email)->first()) {
                $user->forceFill(['role' => Role::Admin, 'email_verified_at' => $user->email_verified_at ?? now()])->save();
                $log[] = "{$email} is now an administrator.";
            } else {
                $password = Str::password(16, symbols: false);
                $user = new User(['name' => 'Administrator', 'email' => $email, 'password' => $password]);
                $user->forceFill(['role' => Role::Admin, 'email_verified_at' => now()])->save();
                $log[] = "Created administrator {$email}\nTemporary password: {$password}\n"
                    .'Log in at '.url('/login').' and change it under Profile right away. This password is shown only once.';
            }
        }

        // ?mailtest=1 - send a test email and show the exact SMTP error if it fails.
        if ($request->boolean('mailtest')) {
            $to = (string) config('itgurus.notify_email');
            try {
                Mail::raw('Test email from '.config('app.url').' - mail is working.', fn ($m) => $m->to($to)->subject('IT GURUs mail test'));
                $log[] = "MAIL OK - test email sent to {$to} (from ".config('mail.from.address').').';
            } catch (Throwable $e) {
                $log[] = 'MAIL FAILED: '.$e->getMessage()
                    ."\nCheck MAIL_HOST / MAIL_PORT / MAIL_SCHEME / MAIL_USERNAME / MAIL_PASSWORD in .env."
                    ."\nMAIL_FROM_ADDRESS should be the same mailbox as MAIL_USERNAME.";
            }
        }

        // ?log=1 - show the end of the newest error log (no SSH needed).
        if ($request->boolean('log')) {
            $files = glob(storage_path('logs/*.log')) ?: [];
            usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));
            if ($files) {
                $lines = preg_split('/\R/', (string) file_get_contents($files[0]));
                $errors = array_values(array_filter($lines, fn ($l) => preg_match('/^\[\d{4}-\d\d-\d\d .*?\] \w+\.(ERROR|CRITICAL|ALERT|EMERGENCY|WARNING)/', $l)));
                $log[] = 'Latest log entries from '.basename($files[0]).":\n".(implode("\n", array_map(
                    fn ($l) => mb_strimwidth($l, 0, 400, '…'), array_slice($errors, -10)
                )) ?: '(no errors logged)');
            } else {
                $log[] = 'No log files yet.';
            }
        }

        $log[] = $ok ? 'SETUP OK' : 'SETUP FAILED - see the errors above.';

        return response(implode("\n\n", $log)."\n", $ok ? 200 : 500)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'no-store');
    }
}
