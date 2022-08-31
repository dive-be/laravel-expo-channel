<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use RuntimeException;
use UnexpectedValueException;

/**
 * @internal
 */
final class ExpoChannel
{
    /**
     * The channel's name.
     */
    public const NAME = 'expo';

    /**
     * Create a new channel instance.
     */
    public function __construct(
        private readonly ExpoClient $client,
        private readonly Dispatcher $events,
    ) {}

    /**
     * Send the notification to Expo's Push API.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $tokens = $this->getTokens($notifiable, $notification);

        if (! count($tokens)) {
            return;
        }

        $message = $this->getMessage($notifiable, $notification);

        $response = $this->client->sendPushNotifications(
            ExpoEnvelope::create($tokens, $message)
        );

        if ($response->failure) {
            $this->dispatchFailedEvents($notifiable, $notification, $response->errors);
        }
    }

    /**
     * Dispatch failed events for notifications that weren't delivered.
     */
    private function dispatchFailedEvents(object $notifiable, Notification $notification, array $errors): void
    {
        foreach ($errors as $error) {
            $this->events->dispatch(new NotificationFailed($notifiable, $notification, self::NAME, $error));
        }
    }

    /**
     * Get the message that should be delivered.
     */
    private function getMessage(object $notifiable, Notification $notification): ExpoMessage
    {
        if (! method_exists($notification, 'toExpo')) {
            throw new RuntimeException('Notification is missing the toExpo method.');
        }

        $message = $notification->toExpo($notifiable);

        if (! $message instanceof ExpoMessage) {
            throw new UnexpectedValueException(sprintf('toExpo must return an instance of %s', ExpoMessage::class));
        }

        return $message;
    }

    /**
     * Get the recipients that the message should be delivered to.
     *
     * @return array<ExpoPushToken>
     */
    private function getTokens(object $notifiable, Notification $notification): array
    {
        if (! method_exists($notifiable, 'routeNotificationFor')) {
            throw new RuntimeException('You must provide an instance of Notifiable.');
        }

        $tokens = $notifiable->routeNotificationFor(self::NAME, $notification);

        if ($tokens instanceof Arrayable) {
            $tokens = $tokens->toArray();
        }

        return Arr::wrap($tokens);
    }
}
