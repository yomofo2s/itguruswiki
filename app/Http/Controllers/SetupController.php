<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Throwable;

/**
 * Browser-triggered maintenance for hosting plans without SSH or cron.
 *
 * Enabled only while ITG_SETUP_TOKEN (min. 32 characters) is set in .env:
 *   https://www.itgurusgermany.com/_setup/<token>                      -> migrate + seed + storage link
 *   https://www.itgurusgermany.com/_setup/<token>?admin=you@mail.com   -> also promote that account to admin
 *
 * Remove ITG_SETUP_TOKEN from .env when you are done - the URL then returns 404.
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

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

        $log[] = $ok ? 'SETUP OK - remove ITG_SETUP_TOKEN from .env when you are finished.' : 'SETUP FAILED - see the errors above.';

        return response(implode("\n\n", $log)."\n", $ok ? 200 : 500)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'no-store');
    }
}
