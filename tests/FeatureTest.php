<?php declare(strict_types=1);

namespace Tests;

use NotificationChannels\Expo\ExpoGateway;
use NotificationChannels\Expo\ExpoServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class FeatureTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app->bind(ExpoGateway::class, InMemoryExpoGateway::class);
    }

    protected function getPackageProviders($app): array
    {
        return [ExpoServiceProvider::class];
    }
}
