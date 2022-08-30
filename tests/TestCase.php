<?php declare(strict_types=1);

namespace Tests;

use NotificationChannels\Expo\ExpoServiceProvider;
use Orchestra\Testbench\TestCase as TestCaseBase;

class TestCase extends TestCaseBase
{
    protected function getPackageProviders($app): array
    {
        return [ExpoServiceProvider::class];
    }
}
