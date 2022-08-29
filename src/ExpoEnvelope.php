<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Support\Jsonable;

/**
 * @internal
 */
final class ExpoEnvelope implements Jsonable
{
    private function __construct(
        public readonly array $recipients,
        public readonly ExpoMessage $message,
    ) {
        throw_unless(count($this->recipients), 'There must be at least 1 recipient.');
    }

    public static function create(array $recipients, ExpoMessage $message): self
    {
        return new self($recipients, $message);
    }

    public function toJson($options = 0): string
    {
        return json_encode(get_object_vars($this), $options);
    }
}
