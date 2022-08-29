<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

final class AsExpoPushToken implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ExpoPushToken
    {
        return is_string($value) ? ExpoPushToken::fromString($value) : $value;
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        if (is_string($value)) {
            $value = $this->get($model, $key, $value, $attributes);
        }

        return $value->asString();
    }
}
