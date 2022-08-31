<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Dive\Utils\Makeable;
use Illuminate\Contracts\Validation\InvokableRule;
use UnexpectedValueException;

final class ExpoPushTokenRule implements InvokableRule
{
    use Makeable;

    /**
     * Run the rule and determine whether the value is a valid push token.
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (! is_string($value)) {
            $fail('validation.string')->translate();

            return;
        }

        try {
            ExpoPushToken::make($value);
        } catch (UnexpectedValueException $ex) {
            $fail($ex->getMessage());
        }
    }
}
