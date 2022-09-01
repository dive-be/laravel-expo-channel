<?php declare(strict_types=1);

namespace NotificationChannels\Expo\Exceptions;

use Exception;

final class CouldNotSendNotification extends Exception
{
    public static function invalidNotifiable(): self
    {
        return new self('You must provide an instance of Notifiable.');
    }

    public static function missingMessage(): self
    {
        return new self('Notification is missing the toExpo method.');
    }

    public static function serviceRespondedWithAnError(string $message): self
    {
        return new self("Expo responded with an error: {$message}");
    }
}
