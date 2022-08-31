<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoClient;
use NotificationChannels\Expo\ExpoEnvelope;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;

trait ExpoClientContractTests
{
    abstract protected function client(): ExpoClient;

    /** @test */
    public function it_responds_with_failure_when_invalid_tokens_are_supplied()
    {
        $envelope = ExpoEnvelope::make([
            ExpoPushToken::make('ExpoPushToken[Wi54gvIrap4SDW4Dsh6b0h]'),
            $token = ExpoPushToken::make('ExpoPushToken[zblQYn7ReoYrLoHYsXSe0q]')
        ], ExpoMessage::create('John', 'Cena'));

        $response = $this->client()->sendPushNotifications($envelope);

        $this->assertTrue($response->failure);
        $this->assertCount(2, $response->errors);

        $error = $response->errors[1];

        $this->assertTrue($error->token->equals($token));
        $this->assertTrue($error->type->isDeviceNotRegistered());
    }
}
