<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use NotificationChannels\Expo\Gateway\ExpoEnvelope;
use NotificationChannels\Expo\Gateway\ExpoGateway;

trait ExpoGatewayContractTests
{
    abstract protected function gateway(): ExpoGateway;

    /** @test */
    public function it_responds_with_failure_when_invalid_tokens_are_supplied()
    {
        $envelope = ExpoEnvelope::make([
            ExpoPushToken::make('ExpoPushToken[Wi54gvIrap4SDW4Dsh6b0h]'),
            $token = ExpoPushToken::make('ExpoPushToken[zblQYn7ReoYrLoHYsXSe0q]')
        ], ExpoMessage::create('John', 'Cena'));

        $response = $this->gateway()->sendPushNotifications($envelope);

        $this->assertTrue($response->isFailure());
        $this->assertCount(2, $errors = $response->errors());

        [, $error] = $errors;

        $this->assertTrue($error->token->equals($token));
        $this->assertTrue($error->type->isDeviceNotRegistered());
    }
}
