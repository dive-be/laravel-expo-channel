<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Dive\Utils\Makeable;

final class ExpoError
{
    use Makeable;

    /**
     * Create an ExpoError instance.
     */
    private function __construct(
        public readonly ExpoPushToken $token,
        public readonly ExpoErrorType $type,
        public readonly string $message,
    ) {}
}
