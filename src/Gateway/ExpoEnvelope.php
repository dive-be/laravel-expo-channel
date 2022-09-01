<?php declare(strict_types=1);

namespace NotificationChannels\Expo\Gateway;

use Dive\Utils\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use UnexpectedValueException;

/**
 * @internal
 */
final class ExpoEnvelope implements Arrayable, Jsonable
{
    use Makeable;

    /**
     * Create a new ExpoEnvelope instance.
     *
     * @param array<int, ExpoPushToken> $recipients
     */
    private function __construct(
        public readonly array $recipients,
        public readonly ExpoMessage $message,
    ) {
        if (! count($recipients)) {
            throw new UnexpectedValueException('There must be at least 1 recipient.');
        }
    }

    /**
     * Get the ExpoEnvelope instance as an array.
     */
    public function toArray(): array
    {
        $envelope = $this->message->toArray();
        $envelope['to'] = array_map(static fn (ExpoPushToken $token) => $token->asString(), $this->recipients);

        return $envelope;
    }

    /**
     * Convert the ExpoEnvelope instance to its JSON representation.
     *
     * @param int $options
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options) ?: '';
    }
}
