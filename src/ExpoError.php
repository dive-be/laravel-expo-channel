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
        public readonly ExpoErrorType $type,
        public readonly ExpoPushToken $token,
        public readonly string $message,
    ) {}
}
