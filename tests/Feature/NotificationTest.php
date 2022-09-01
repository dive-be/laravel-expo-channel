<?php declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use NotificationChannels\Expo\ExpoError;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use Tests\FeatureTest;
use Tests\InMemoryExpoGateway;

final class NotificationTest extends FeatureTest
{
    protected function defineWebRoutes($router)
    {
        $router->patch('notification/send', static function (Request $request) {
            $user = new User($request->only('token'));

            $user->notify(new PartnerHasReplied());

            return ['message' => 'ok'];
        });
    }

    /** @test */
    public function test_user_can_be_notified()
    {
        Event::fake(NotificationFailed::class);

        $this->patchJson('notification/send', ['token' => InMemoryExpoGateway::class]);

        Event::assertNotDispatched(NotificationFailed::class);
    }

    /** @test */
    public function test_listeners_are_invoked_on_failure()
    {
        Event::listen(NotificationFailed::class, function ($event) {
            $this->assertSame('expo', $event->channel);
            $this->assertInstanceOf(ExpoError::class, $event->data);
        });

        $this->patchJson('notification/send', ['token' => 'ExpoPushToken[834VY1z7Yg2kQPSVsC8TFp]']);
    }
}

final class User extends Authenticatable
{
    use Notifiable;

    protected $casts = ['token' => ExpoPushToken::class];
    protected $guarded = [];

    public function routeNotificationForExpo(): ExpoPushToken
    {
        return $this->token;
    }
}

final class PartnerHasReplied extends Notification
{
    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create('New message', 'I hate you.');
    }

    public function via(): array
    {
        return ['expo'];
    }
}
