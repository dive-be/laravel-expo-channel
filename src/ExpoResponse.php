<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use GuzzleHttp\Psr7\Response;

/**
 * @internal
 */
final class ExpoResponse
{
    private function __construct(
        public readonly bool $failure,
        public readonly array $errors,
    ) {}

    public static function fromGuzzle(Response $response): self
    {
        $body = json_decode((string) $response->getBody(), true);

        return new self(
            $body['data']['status'] !== 'ok' ?? true,
            $body['errors'] ?? [],
        );
    }
}
