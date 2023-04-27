<?php declare(strict_types=1);

namespace NotificationChannels\Expo\Validation;

use Closure;
use Dive\Utils\Makeable;
use Illuminate\Contracts\Validation\ValidationRule;
use NotificationChannels\Expo\ExpoPushToken;
use UnexpectedValueException;

final readonly class ExpoPushTokenRule implements ValidationRule
{
    use Makeable;

    /**
     * Run the rule and determine whether the value is a valid push token.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('validation.string')->translate();

            return;
        }

        try {
            ExpoPushToken::make($value);
        } catch (UnexpectedValueException) {
            $fail('validation.regex')->translate();
        }
    }
}
