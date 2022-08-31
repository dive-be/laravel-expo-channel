<?php declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Events\Dispatcher;
use Illuminate\Events\NullDispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Testing\Fakes\EventFake;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoError;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use PHPUnit\Framework\TestCase;
use Tests\InMemoryExpoClient;

final class ChannelTest extends TestCase
{
    private ExpoChannel $channel;

    private InMemoryExpoClient $client;

    private EventFake $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new InMemoryExpoClient();
        $this->events = new EventFake(new NullDispatcher(new Dispatcher())) ;
        $this->channel = new ExpoChannel($this->client, $this->events);
    }

    /** @test */
    public function it_can_send_a_push_notification()
    {
        $notifiable = new Customer();
        $notification = new FoodWasDelivered();

        $this->client->assertNothingSent();

        $this->channel->send($notifiable, $notification);

        $this->client->assertSent($notifiable->routeNotificationForExpo(), $notification->toExpo());
        $this->events->assertNotDispatched(NotificationFailed::class);
    }

    /** @test */
    public function it_dispatches_failed_events_when_something_goes_wrong()
    {
        $notifiable = new FraudulentCustomer();
        $notification = new FoodWasDelivered();

        $this->events->assertNothingDispatched();

        $this->channel->send($notifiable, $notification);

        $this->events->assertDispatched(NotificationFailed::class,
            static fn (NotificationFailed $event) => $event->channel === 'expo' && $event->data instanceof ExpoError
        );
    }
}

final class FoodWasDelivered extends Notification
{
    public function toExpo(): ExpoMessage
    {
        return ExpoMessage::create('Food Delivered')
            ->body('Your food was delivered on time!')
            ->playSound();
    }
}

final class Customer
{
    use Notifiable;

    public function routeNotificationForExpo(): ExpoPushToken
    {
        return ExpoPushToken::make(InMemoryExpoClient::VALID_TOKEN);
    }
}

final class FraudulentCustomer
{
    use Notifiable;

    public function routeNotificationForExpo(): ExpoPushToken
    {
        return ExpoPushToken::make('ExpoPushToken[RmddzXcd66CsTIkQnCpYPa]');
    }
}
