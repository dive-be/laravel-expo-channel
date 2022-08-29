<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use GuzzleHttp\Client;

/**
 * @internal
 */
final class ExpoClient
{
    private const BASE_URL = 'https://exp.host/--/api/v2';
    private const KIBIBYTE = 1024;
    private const THRESHOLD = 1;

    private Client $http;

    public function __construct(?string $accessToken)
    {
        $this->http = new Client([
            'base_uri' => self::BASE_URL,
            'headers' => array_filter([
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
                'Authorization' => is_string($accessToken) ? "Bearer {$accessToken}" : null,
                'Content-Type' => 'application/json',
                'Host' => 'exp.host',
            ]),
        ]);
    }

    public function sendPushNotifications(ExpoEnvelope $envelope): ExpoResponse
    {
        [$headers, $body] = $this->compressUsingGzip($envelope->toJson());

        $response = $this->http->post('/push/send', ['body' => $body, 'headers' => $headers, 'http_errors' => false]);

        return ExpoResponse::fromGuzzle($response);
    }

    private function compressUsingGzip(string $value): array
    {
        if (mb_strlen($value) / self::KIBIBYTE <= self::THRESHOLD) {
            return [[], $value];
        }

        $encoded = gzencode($value, 6);

        if ($encoded === false) {
            return [[], $value];
        }

        return [['Content-Encoding' => 'gzip'], $encoded];
    }
}
