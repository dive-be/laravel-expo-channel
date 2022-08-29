<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

/**
 * @internal
 */
final class ExpoChannel
{
    public const NAME = 'expo';

    public function __construct(
        private ExpoClient $client,
        private Dispatcher $events,
    ) {}

    public function send(mixed $notifiable, Notification $notification)
    {
        $tokens = (array) $notifiable->routeNotificationFor(self::NAME, $notification);

        if (! count($tokens)) {
            return;
        }

        throw_unless(method_exists($notification, 'toExpo'), 'Notification is missing toExpo method.');

        $msg = $notification->toExpo($notifiable);

        throw_unless($msg instanceof ExpoMessage, sprintf('toExpo must return an instance of %s', ExpoMessage::class));

        $response = $this->client->sendPushNotifications(ExpoEnvelope::create($tokens, $msg));

        if ($response->isFailure()) {
            $this->dispatchFailedEvents($notifiable, $notification, $response->errors());
        }
    }

    private function dispatchFailedEvents(mixed $notifiable, Notification $notification, array $errors)
    {
        foreach ($errors as $error) {
            $this->events->dispatch(new NotificationFailed($notifiable, $notification, self::NAME, $error));
        }
    }
}
