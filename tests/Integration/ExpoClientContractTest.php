<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoClient;
use NotificationChannels\Expo\ExpoClientUsingGuzzle;
use NotificationChannels\Expo\ExpoEnvelope;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use PHPUnit\Framework\TestCase;
use Tests\InMemoryExpoClient;

final class ExpoClientContractTest extends TestCase
{
    /**
     * @dataProvider clients
     * @test
     */
    public function it_responds_with_failure_when_invalid_tokens_are_supplied(ExpoClient $client)
    {
        $envelope = ExpoEnvelope::make([
            ExpoPushToken::make('ExpoPushToken[Wi54gvIrap4SDW4Dsh6b0h]'),
            $token = ExpoPushToken::make('ExpoPushToken[zblQYn7ReoYrLoHYsXSe0q]')
        ], ExpoMessage::create('John', 'Cena'));

        $response = $client->sendPushNotifications($envelope);

        $this->assertTrue($response->failure);
        $this->assertCount(2, $response->errors);

        $error = $response->errors[1];

        $this->assertTrue($error->token->equals($token));
        $this->assertTrue($error->type->isDeviceNotRegistered());
    }

    /**
     * It is practically impossible (need physical device) to test the happy path for the real service.
     * Mocking the requests will yield no benefit at all, so we are not going to test it.
     *
     * @test
     */
    public function it_responds_with_ok_when_all_tokens_are_valid()
    {
        $envelope = ExpoEnvelope::make([
            ExpoPushToken::make(InMemoryExpoClient::VALID_TOKEN),
        ], ExpoMessage::create('John', 'Cena'));

        $response = (new InMemoryExpoClient())->sendPushNotifications($envelope);

        $this->assertFalse($response->failure);
        $this->assertCount(0, $response->errors);
    }

    protected function clients(): array
    {
        return [
            [new InMemoryExpoClient()],
            [new ExpoClientUsingGuzzle()],
        ];
    }
}
