<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

/**
 * @internal
 */
final class ExpoResponse
{
    private const FAILED = 'failed';
    private const FATAL = 'fatal';
    private const OK = 'ok';

    /**
     * Create a new ExpoResponse instance.
     *
     * @param $errors array<int, ExpoError>
     */
    private function __construct(
        private readonly string $type,
        private readonly array|string|null $context = null,
    ) {}

    /**
     * Create a "failed" ExpoResponse instance.
     *
     * @param $errors array<int, ExpoError>
     */
    public static function failed(array $errors): self
    {
        return new self(self::FAILED, $errors);
    }

    /**
     * Create a "fatal" ExpoResponse instance.
     */
    public static function fatal(string $message): self
    {
        return new self(self::FATAL, $message);
    }

    /**
     * Create an "ok" ExpoResponse instance.
     */
    public static function ok(): self
    {
        return new self(self::OK);
    }

    public function errors(): array
    {
        return is_array($this->context) ? $this->context : [];
    }

    public function isFatal(): bool
    {
        return $this->type === self::FATAL;
    }

    public function isFailure(): bool
    {
        return $this->type === self::FAILED;
    }

    public function isOk(): bool
    {
        return $this->type === self::OK;
    }

    public function message(): string
    {
        return is_string($this->context) ? $this->context : '';
    }
}
