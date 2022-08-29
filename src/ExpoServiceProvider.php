<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;

final class ExpoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->afterResolving(ChannelManager::class, $this->extendManager(...));
        $this->app->singleton(ExpoChannel::class, $this->createExpoChannel(...));
    }

    private function createExpoChannel(Application $app): ExpoChannel
    {
        $client = new ExpoClient($app['config']['services.expo.access_token']);

        return new ExpoChannel($client, $app['events']);
    }

    private function extendManager(ChannelManager $cm)
    {
        $cm->extend(ExpoChannel::NAME, static fn (Application $app) => $app->make(ExpoChannel::class));
    }
}
