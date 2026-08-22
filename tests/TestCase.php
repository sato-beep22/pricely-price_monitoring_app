<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Prompts\Prompt;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        Mockery::close();
        parent::setUp();
        Prompt::fallbackWhen(false);
    }

    /**
     * Override migrate:fresh to always pass --force, preventing the
     * ConfirmableTrait from triggering the Prompts fallback during tests.
     *
     * @return array<string, mixed>
     */
    protected function migrateFreshUsing()
    {
        return array_merge(parent::migrateFreshUsing(), ['--force' => true]);
    }

    /**
     * Bypass PendingCommand (which mocks OutputStyle via Mockery) so that
     * migrate:fresh can run without hitting the Mockery expectation guard on
     * the OutputStyle::askQuestion() method.
     */
    protected function migrateDatabases()
    {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', $this->migrateFreshUsing());
    }
}

