<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use GuzzleHttp\Psr7\Response;

/**
 * @internal
 */
final class ExpoResponse
{
    /**
     * Create a new ExpoResponse instance.
     */
    private function __construct(
        public readonly bool $failure,
        public readonly array $errors,
    ) {}

    /**
     * Create a new ExpoResponse instance from a given Response.
     */
    public static function fromGuzzle(Response $response): self
    {
        $body = json_decode((string) $response->getBody(), true);

        return new self(
            $body['data']['status'] !== 'ok' ?? true,
            $body['errors'] ?? [],
        );
    }

    /**
     * Create a "failed" ExpoResponse instance.
     */
    public static function failure(array $errors): self
    {
        return new self(true, $errors);
    }

    /**
     * Create an "ok" ExpoResponse instance.
     */
    public static function ok(): self
    {
        return new self(false, []);
    }
}
