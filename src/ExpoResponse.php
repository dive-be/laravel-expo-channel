<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

/**
 * @internal
 */
final class ExpoResponse
{
    /**
     * Create a new ExpoResponse instance.
     *
     * @param $errors array<int, ExpoError>
     */
    private function __construct(
        public readonly bool $failure,
        public readonly array $errors = [],
    ) {}

    /**
     * Create a "failed" ExpoResponse instance.
     *
     * @param $errors array<int, ExpoError>
     */
    public static function failure(array $errors): self
    {
        return new self(true, $errors);
    }

    /**
     * Create an "ok" ExpoResponse instance.
     */
    public static function ok(): self
    {
        return new self(false);
    }
}
