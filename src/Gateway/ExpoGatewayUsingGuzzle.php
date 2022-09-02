<?php declare(strict_types=1);

namespace NotificationChannels\Expo\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use NotificationChannels\Expo\ExpoError;
use NotificationChannels\Expo\ExpoErrorType;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class ExpoGatewayUsingGuzzle implements ExpoGateway
{
    /**
     * Expo's Push API URL.
     */
    private const BASE_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * OK status code.
     */
    private const HTTP_OK = 200;

    /**
     * 1 KiB in bytes.
     */
    private const KIBIBYTE = 1024;

    /**
     * The threshold (in KiB) determines whether a payload needs to be compressed.
     */
    private const THRESHOLD = 1;

    /**
     * The Guzzle HTTP client instance.
     */
    private readonly Client $http;

    /**
     * Create a new ExpoClient instance.
     */
    public function __construct(?string $accessToken = null)
    {
        $this->http = new Client([
            RequestOptions::HEADERS => array_filter([
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

        $response = $this->http->post(self::BASE_URL, [
            RequestOptions::BODY => $body,
            RequestOptions::HEADERS => $headers,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($response->getStatusCode() !== self::HTTP_OK) {
            return ExpoResponse::fatal((string) $response->getBody());
        }

        $tickets = $this->getPushTickets($response);
        $errors = $this->getPotentialErrors($envelope->recipients, $tickets);

        return count($errors) ? ExpoResponse::failed($errors) : ExpoResponse::ok();
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

    /**
     * Get an array of potential errors responded by the service.
     *
     * @param $tokens array<int, \NotificationChannels\Expo\ExpoPushToken>
     *
     * @return array<int, ExpoError>
     */
    private function getPotentialErrors(array $tokens, array $tickets): array
    {
        $errors = [];

        foreach ($tickets as $idx => $ticket) {
            if ($ticket['status'] === 'ok') {
                continue;
            }

            $errors[] = ExpoError::make(
                $tokens[$idx],
                ExpoErrorType::from($ticket['details']['error']),
                $ticket['message'],
            );
        }

        return $errors;
    }

    /**
     * Get the array of push tickets responded by the service.
     */
    private function getPushTickets(ResponseInterface $response): array
    {
        $body = json_decode((string) $response->getBody(), true);

        return Arr::get($body, 'data', []); // @phpstan-ignore-line
    }
}
