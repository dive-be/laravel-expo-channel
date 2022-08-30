<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Validation\InvokableRule;
use UnexpectedValueException;

final class ExpoPushTokenRule implements InvokableRule
{
    /**
     * Create a new ExpoPushTokenRule instance.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Run the rule and determine whether the value is a valid push token.
     */
    public function __invoke($attribute, $value, $fail): void
    {
        try {
            ExpoPushToken::fromString($value);
        } catch (UnexpectedValueException $ex) {
            $fail($ex->getMessage());
        }
    }
}
