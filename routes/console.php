<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

Artisan::command('app:create-admin {email?} {--name=}', function (?string $email = null) {
    $email ??= text('Email address', required: true);
    $name = $this->option('name') ?: text('Name', default: 'Administrator', required: true);
    $password = password('Password (min. 12 characters)', required: true);

    $validator = Validator::make(
        compact('email', 'name', 'password'),
        ['email' => 'required|email', 'name' => 'required|max:120', 'password' => 'required|min:12'],
    );

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::firstOrNew(['email' => $email]);
    $user->name = $name;
    $user->password = $password;
    $user->role = Role::Admin;
    $user->email_verified_at ??= now();
    $user->save();

    $this->info("Administrator {$email} is ready.");

    return 0;
})->purpose('Create or promote an administrator account');

// Shared hosting has no long-running workers: the cron-driven scheduler
// processes queued mail every minute and cleans up old records.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
Schedule::command('queue:prune-failed --hours=168')->daily();
Schedule::command('auth:clear-resets')->daily();
