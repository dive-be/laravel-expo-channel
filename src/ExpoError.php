<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Dive\Utils\Makeable;

final readonly class ExpoError
{
    use Makeable;

    /**
     * Create an ExpoError instance.
     */
    private function __construct(
        public ExpoErrorType $type,
        public ExpoPushToken $token,
        public string $message,
    ) {}
}
