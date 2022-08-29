<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use GuzzleHttp\Psr7\Response;

/**
 * @internal
 */
final class ExpoResponse
{
    private function __construct(
        private bool $ok,
        private array $errors,
    ) {}

    public static function fromGuzzle(Response $response): self
    {
        $body = json_decode($response->getBody(), true);

        return new self(
            $body['data']['status'] === 'ok' ?? false,
            $body['errors'] ?? [],
        );
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function isFailure(): bool
    {
        return ! $this->ok;
    }
}
