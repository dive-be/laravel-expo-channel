<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * @internal
 */
final class ExpoEnvelope implements Arrayable, Jsonable
{
    private function __construct(
        public readonly array $recipients,
        public readonly ExpoMessage $message,
    ) {
        throw_unless(count($this->recipients), 'There must be at least 1 recipient.');
    }

    public static function create(array $recipients, ExpoMessage $message): self
    {
        return new self($recipients, $message);
    }

    public function toArray(): array
    {
        $envelope = $this->message->toArray();

        $envelope['to'] = array_map(static fn ($recipient) => (string) $recipient, $this->recipients);

        return $envelope;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
