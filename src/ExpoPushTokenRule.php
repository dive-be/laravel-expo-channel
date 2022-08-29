<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Validation\InvokableRule;
use InvalidArgumentException;

final class ExpoPushTokenRule implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        try {
            ExpoPushToken::fromString($value);
        } catch (InvalidArgumentException $ex) {
            $fail($ex->getMessage());
        }
    }
}
