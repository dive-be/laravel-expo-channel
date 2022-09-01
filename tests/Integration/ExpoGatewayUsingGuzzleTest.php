<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\Gateway\ExpoGateway;
use NotificationChannels\Expo\Gateway\ExpoGatewayUsingGuzzle;
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
