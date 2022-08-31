<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

final class AsExpoPushToken implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values to an ExpoPushToken.
     */
    public function get($model, string $key, $value, array $attributes): ExpoPushToken
    {
        if (is_string($value)) {
            return ExpoPushToken::make($value);
        }

        if ($value instanceof ExpoPushToken) {
            return $value;
        }

        throw new InvalidArgumentException('The given value cannot be cast to an instance of ExpoPushToken.');
    }

    /**
     * Transform the attribute to its underlying model values from an ExpoPushToken.
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_string($value)) {
            $value = $this->get($model, $key, $value, $attributes);
        }

        if ($value instanceof ExpoPushToken) {
            return $value->asString();
        }

        throw new InvalidArgumentException('The given value cannot be serialized as a valid ExpoPushToken.');
    }
}
