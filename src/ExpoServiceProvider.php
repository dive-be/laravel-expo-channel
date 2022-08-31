<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class ExpoServiceProvider extends ServiceProvider
{
    /**
     * Register the Expo application services.
     */
    public function register(): void
    {
        $this->app->afterResolving(ChannelManager::class, $this->extendManager(...));
        $this->app->singleton(ExpoChannel::class, $this->createExpoChannel(...));
    }

    /**
     * Create a new ExpoChannel instance.
     */
    private function createExpoChannel(Application $app): ExpoChannel
    {
        /** @var Repository $config */
        $config = $app->make(Repository::class);

        $accessToken = $config->get('services.expo.access_token');

        if (! is_null($accessToken) && ! is_string($accessToken)) {
            throw new RuntimeException('The provided access token is not a valid Expo Access Token.');
        }

        /** @var Dispatcher $events */
        $events = $app->make(Dispatcher::class);

        return new ExpoChannel(new ExpoClientUsingGuzzle($accessToken), $events);
    }

    /**
     * Extend the ChannelManager with ExpoChannel.
     */
    private function extendManager(ChannelManager $cm): void
    {
        $cm->extend(ExpoChannel::NAME, static fn (Application $app) => $app->make(ExpoChannel::class));
    }
}
