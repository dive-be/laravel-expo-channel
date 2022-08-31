<?php declare(strict_types=1);

namespace Tests;

use NotificationChannels\Expo\ExpoClient;
use NotificationChannels\Expo\ExpoEnvelope;
use NotificationChannels\Expo\ExpoError;
use NotificationChannels\Expo\ExpoErrorType;
use NotificationChannels\Expo\ExpoPushToken;
use NotificationChannels\Expo\ExpoResponse;

final class InMemoryExpoClient implements ExpoClient
{
    public const VALID_TOKEN = 'ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]';

    public function sendPushNotifications(ExpoEnvelope $envelope): ExpoResponse
    {
        $errors = [];

        foreach ($envelope->recipients as $token) {
            if (! $token->equals(self::VALID_TOKEN)) {
                $errors[] = $this->newDeviceError($token);
            }
        }

        return count($errors) ? ExpoResponse::failure($errors) : ExpoResponse::ok();
    }

    private function newDeviceError(ExpoPushToken $token): ExpoError
    {
        return ExpoError::make(
            $token,
            ExpoErrorType::DeviceNotRegistered,
            "{$token} is not a registered push notification recipient"
        );
    }
}
