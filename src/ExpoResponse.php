<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use Psr\Http\Message\ResponseInterface;

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
    public static function fromGuzzle(ResponseInterface $response): self
    {
        $body = json_decode((string) $response->getBody(), true);

        if (! is_array($body) || array_is_list($body)) {
            return self::failure([]);
        }

        $isFailure = ($body['data']['status'] ?? 'error') === 'error';

        return $isFailure ? self::failure($body['errors']) : self::ok();
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
