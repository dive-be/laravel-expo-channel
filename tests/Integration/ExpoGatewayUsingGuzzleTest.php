<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoGateway;
use NotificationChannels\Expo\ExpoGatewayUsingGuzzle;
use PHPUnit\Framework\TestCase;

/**
 * @group network
 */
final class ExpoGatewayUsingGuzzleTest extends TestCase
{
    use ExpoGatewayContractTests;

    protected function gateway(): ExpoGateway
    {
        return new ExpoGatewayUsingGuzzle();
    }
}
