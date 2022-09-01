<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Dive\Utils\Makeable;
use Illuminate\Contracts\Database\Eloquent\Castable;
use NotificationChannels\Expo\Casts\AsExpoPushToken;
use NotificationChannels\Expo\Validation\ExpoPushTokenRule;
use JsonSerializable;
use Stringable;
use UnexpectedValueException;

final class ExpoPushToken implements Castable, JsonSerializable, Stringable
{
    use Makeable;

    /**
     * The minimum acceptable length of a push token.
     */
    public const MIN_LENGTH = 16;

    /**
     * The string representation of the push token.
     */
    private readonly string $value;

    /**
     * Create a new ExpoPushToken instance.
     *
     * @throws UnexpectedValueException
     */
    private function __construct(string $value)
    {
        if (mb_strlen($value) < self::MIN_LENGTH) {
            throw new UnexpectedValueException("{$value} is not a valid push token.");
        }

        if (! str_starts_with($value, 'ExponentPushToken[') && ! str_starts_with($value, 'ExpoPushToken[')) {
            throw new UnexpectedValueException("{$value} is not a valid push token.");
        }

        if (! str_ends_with($value, ']')) {
            throw new UnexpectedValueException("{$value} is not a valid push token.");
        }

        $this->value = $value;
    }

    /**
     * Get the FQCN of the caster to use when casting from / to an ExpoPushToken.
     */
    public static function castUsing(array $arguments): string
    {
        return AsExpoPushToken::class;
    }

    /**
     * Get the rule to validate an expo push token.
     */
    public static function rule(): ExpoPushTokenRule
    {
        return ExpoPushTokenRule::make();
    }

    /**
     * @see __toString()
     */
    public function asString(): string
    {
        return $this->value;
    }

    /**
     * Determine whether a given token is equal.
     */
    public function equals(self|string $other): bool
    {
        if ($other instanceof self) {
            $other = $other->asString();
        }

        return $other === $this->asString();
    }

    /**
     * Convert the ExpoPushToken instance to its JSON representation.
     */
    public function jsonSerialize(): string
    {
        return $this->asString();
    }

    /**
     * Get the string representation of the push token.
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}
