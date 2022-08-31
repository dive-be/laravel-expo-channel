<?php declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Notifications\ChannelManager;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoClient;
use NotificationChannels\Expo\ExpoClientUsingGuzzle;
use NotificationChannels\Expo\ExpoServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ServiceBindingsTest extends TestCase
{
    use CreatesApplication;

    /** @test */
    public function it_binds_the_expo_guzzle_client_to_the_container()
    {
        $app = $this->createApplication();
        $app->register(ExpoServiceProvider::class);

        $client = $app->make(ExpoClient::class);

        $this->assertInstanceOf(ExpoClientUsingGuzzle::class, $client);
        $this->assertNotSame($client, $app->make(ExpoClient::class));
    }

    /** @test */
    public function it_throws_if_an_invalid_access_token_is_passed_to_the_client()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The provided access token is not a valid Expo Access Token.');

        $app = $this->createApplication();
        $app->register(ExpoServiceProvider::class);

        $app['config']['services.expo.access_token'] = 123456789;

        $app->make(ExpoClient::class);
    }

    /** @test */
    public function it_binds_the_expo_channel_as_a_singleton_to_the_container()
    {
        $app = $this->createApplication();
        $app->register(ExpoServiceProvider::class);

        $expo = $app->make(ExpoChannel::class);

        $this->assertSame($expo, $app->make(ExpoChannel::class));
    }

    /** @test */
    public function it_extends_the_channel_manager_with_expo()
    {
        $app = $this->createApplication();
        $app->register(ExpoServiceProvider::class);

        $cm = $app->make(ChannelManager::class);

        $this->assertSame($cm->channel(ExpoChannel::NAME), $app->make(ExpoChannel::class));
    }
}
