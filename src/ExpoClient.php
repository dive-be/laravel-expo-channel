<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

interface ExpoClient
{
    /**
     * Send the notifications to Expo's Push Service.
     */
    public function sendPushNotifications(ExpoEnvelope $envelope): ExpoResponse;
}
