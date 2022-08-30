<?php declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Notifications\ChannelManager;
use NotificationChannels\Expo\ExpoChannel;
use NotificationChannels\Expo\ExpoServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use PHPUnit\Framework\TestCase;

final class ServiceBindingsTest extends TestCase
{
    use CreatesApplication;

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
