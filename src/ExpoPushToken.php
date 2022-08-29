<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Database\Eloquent\Castable;
use InvalidArgumentException;
use Stringable;

final class ExpoPushToken implements Castable, Stringable
{
    public const MIN_LENGTH = 15;

    private string $value;

    private function __construct(string $value)
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw new InvalidArgumentException("{$value} is not a valid push token.");
        }

        if (! str_starts_with($value, 'ExponentPushToken[') && ! str_starts_with($value, 'ExpoPushToken[')) {
            throw new InvalidArgumentException("{$value} is not a valid push token.");
        }

        if (! str_ends_with($value, ']')) {
            throw new InvalidArgumentException("{$value} is not a valid push token.");
        }

        $this->value = $value;
    }

    public static function castUsing(array $arguments): string
    {
        return AsExpoPushToken::class;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function asString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->asString();
    }
}
