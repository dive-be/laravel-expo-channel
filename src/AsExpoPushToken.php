<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

final class AsExpoPushToken implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values to an ExpoPushToken.
     */
    public function get($model, string $key, $value, array $attributes): ExpoPushToken
    {
        return is_string($value) ? ExpoPushToken::fromString($value) : $value;
    }

    /**
     * Transform the attribute to its underlying model values from an ExpoPushToken.
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_string($value)) {
            $value = $this->get($model, $key, $value, $attributes);
        }

        return $value->asString();
    }
}
