<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use NotificationChannels\Expo\Gateway\ExpoEnvelope;
use NotificationChannels\Expo\Gateway\ExpoGateway;
use PHPUnit\Framework\TestCase;
use Tests\InMemoryExpoGateway;

final class InMemoryExpoGatewayTest extends TestCase
{
    use ExpoGatewayContractTests;

    /**
     * It is practically impossible (need physical device) to test the happy path for the real service.
     * Mocking the requests will yield no benefit at all, so we are not going to test it.
     *
     * @test
     */
    public function it_responds_with_ok_when_all_tokens_are_valid()
    {
        $envelope = ExpoEnvelope::make([
            ExpoPushToken::make(InMemoryExpoGateway::VALID_TOKEN),
        ], ExpoMessage::create('John', 'Cena'));

        $response = $this->gateway()->sendPushNotifications($envelope);

        $this->assertTrue($response->isOk());
    }

    /** @test */
    public function it_responds_with_failure_even_if_there_are_valid_ones_among_the_failed()
    {
        $envelope = ExpoEnvelope::make([
            ExpoPushToken::make(InMemoryExpoGateway::VALID_TOKEN),
            ExpoPushToken::make('ExpoPushToken[Wi54gvIrap4SDW4Dsh6b0h]'),
        ], ExpoMessage::create('John', 'Cena'));

        $response = $this->gateway()->sendPushNotifications($envelope);

        $this->assertTrue($response->isFailure());
        $this->assertCount(1, $response->errors());
    }

    protected function gateway(): ExpoGateway
    {
        return new InMemoryExpoGateway();
    }
}
