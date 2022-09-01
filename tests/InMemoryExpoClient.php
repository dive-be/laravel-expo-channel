<?php declare(strict_types=1);

namespace Tests;

use NotificationChannels\Expo\ExpoClient;
use NotificationChannels\Expo\ExpoEnvelope;
use NotificationChannels\Expo\ExpoError;
use NotificationChannels\Expo\ExpoErrorType;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use NotificationChannels\Expo\ExpoResponse;
use PHPUnit\Framework\Assert;

final class InMemoryExpoClient implements ExpoClient
{
    public const VALID_TOKEN = 'ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]';

    private ?ExpoEnvelope $envelope = null;

    private ?string $shouldBail = null;

    public function assertNothingSent()
    {
        Assert::assertNull($this->envelope, 'Push notification was sent unexpectedly.');
    }

    public function assertSent(ExpoPushToken $token, ExpoMessage $message)
    {
        Assert::assertNotNull($this->envelope, 'A push notification was not sent');
        Assert::assertContainsEquals($token, $this->envelope->recipients, "A push notification was not sent to {$token}");
        Assert::assertEquals($message, $this->envelope->message, "The message was not sent to {$token}");
    }

    public function bail(string $message): self
    {
        $this->shouldBail = $message;

        return $this;
    }

    public function sendPushNotifications(ExpoEnvelope $envelope): ExpoResponse
    {
        $this->record($envelope);

        if (is_string($this->shouldBail)) {
            return ExpoResponse::fatal($this->shouldBail);
        }

        $errors = [];

        foreach ($envelope->recipients as $token) {
            if (! $token->equals(self::VALID_TOKEN)) {
                $errors[] = $this->newDeviceError($token);
            }
        }

        return count($errors) ? ExpoResponse::failed($errors) : ExpoResponse::ok();
    }

    private function newDeviceError(ExpoPushToken $token): ExpoError
    {
        return ExpoError::make(
            $token,
            ExpoErrorType::DeviceNotRegistered,
            "{$token} is not a registered push notification recipient"
        );
    }

    private function record(ExpoEnvelope $envelope)
    {
        $this->envelope = $envelope;
    }
}
