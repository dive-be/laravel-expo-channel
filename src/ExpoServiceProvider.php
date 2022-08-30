<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

final class ExpoServiceProvider extends ServiceProvider
{
    /**
     * Register the Expo application services.
     */
    public function register()
    {
        $this->app->afterResolving(ChannelManager::class, $this->extendManager(...));
        $this->app->singleton(ExpoChannel::class, $this->createExpoChannel(...));
    }

    /**
     * Create a new ExpoChannel instance.
     */
    private function createExpoChannel(Application $app): ExpoChannel
    {
        $client = new ExpoClient($app['config']['services.expo.access_token']);

        return new ExpoChannel($client, $app['events']);
    }

    /**
     * Extend the ChannelManager with ExpoChannel.
     */
    private function extendManager(ChannelManager $cm)
    {
        $cm->extend(ExpoChannel::NAME, static fn (Application $app) => $app->make(ExpoChannel::class));
    }
}
