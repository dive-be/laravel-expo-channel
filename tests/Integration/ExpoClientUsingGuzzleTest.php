<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoClient;
use NotificationChannels\Expo\ExpoClientUsingGuzzle;
use PHPUnit\Framework\TestCase;

/**
 * @group network
 */
final class ExpoClientUsingGuzzleTest extends TestCase
{
    use ExpoClientContractTests;

    protected function client(): ExpoClient
    {
        return new ExpoClientUsingGuzzle();
    }
}
