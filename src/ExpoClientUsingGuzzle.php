<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * @internal
 */
final class ExpoClientUsingGuzzle implements ExpoClient
{
    /**
     * Expo's API URL (v2).
     */
    private const BASE_URL = 'https://exp.host/--/api/v2';

    /**
     * 1 KiB in Bytes.
     */
    private const KIBIBYTE = 1024;

    /**
     * The threshold (in KiB) determines whether a payload needs to be compressed.
     */
    private const THRESHOLD = 1;

    /**
     * The Guzzle HTTP client instance.
     */
    private Client $http;

    /**
     * Create a new ExpoClient instance.
     */
    public function __construct(?string $accessToken = null)
    {
        $this->http = new Client([
            'base_uri' => self::BASE_URL,
            'headers' => array_filter([
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
                'Authorization' => is_string($accessToken) ? "Bearer {$accessToken}" : $accessToken,
                'Content-Type' => 'application/json',
                'Host' => 'exp.host',
            ]),
        ]);
    }

    /**
     * Send the notifications to Expo's Push Service.
     */
    public function sendPushNotifications(ExpoEnvelope $envelope): ExpoResponse
    {
        [$headers, $body] = $this->compressUsingGzip($envelope->toJson());

        $response = $this->http->post('/push/send', [
            RequestOptions::BODY => $body,
            RequestOptions::HEADERS => $headers,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return ExpoResponse::fromGuzzle($response);
    }

    /**
     * Compress the given payload if the size is greater than the threshold (1 KiB).
     */
    private function compressUsingGzip(string $payload): array
    {
        if (mb_strlen($payload) / self::KIBIBYTE <= self::THRESHOLD) {
            return [[], $payload];
        }

        $encoded = gzencode($payload, 6);

        if ($encoded === false) {
            return [[], $payload];
        }

        return [['Content-Encoding' => 'gzip'], $encoded];
    }
}
