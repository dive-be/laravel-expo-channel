<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\Gateway\ExpoGateway;
use NotificationChannels\Expo\Gateway\ExpoGatewayUsingGuzzle;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('network')]
final class ExpoGatewayUsingGuzzleTest extends TestCase
{
    use ExpoGatewayContractTests;

    protected function gateway(): ExpoGateway
    {
        return new ExpoGatewayUsingGuzzle();
    }
}
