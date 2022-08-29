<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Events\Dispatcher;
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
        // wip
    }
}
