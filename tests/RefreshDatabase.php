<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase as BaseRefreshDatabase;

/**
 * Laravel's RefreshDatabase, but migrations run through the console kernel
 * directly instead of the output-mocking artisan() helper. Quieter and it
 * also works on minimal PHP runtimes (e.g. WebAssembly) used in some CI sandboxes.
 */
trait RefreshDatabase
{
    use BaseRefreshDatabase;

    protected function migrateDatabases()
    {
        $this->app[Kernel::class]->call('migrate:fresh', $this->migrateFreshUsing());
    }
}
