<?php declare(strict_types=1);

namespace NotificationChannels\Expo;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Conditionable;
use JsonSerializable;
use UnexpectedValueException;

/**
 * Expo Message Request Format.
 *
 * @see ExpoMessage::$https://docs.expo.dev/push-notifications/sending-notifications/#message-request-format
 */
final class ExpoMessage implements Arrayable, JsonSerializable
{
    use Conditionable;

    /**
     * The possible delivery priorities of a message.
     */
    private const PRIORITIES = ['default', 'normal', 'high'];

    /**
     * A JSON object delivered to your app.
     * It may be up to about 4KiB; the total notification payload sent to Apple and Google must be at most 4KiB
     * or else you will get a "Message Too Big" error.
     */
    private ?string $data = null;

    /**
     * The title to display in the notification.
     * Often displayed above the notification body.
     */
    private string $title;

    /**
     * The message to display in the notification.
     */
    private string $body;

    /**
     * Time to Live: the number of seconds for which the message may be kept around for redelivery if it hasn't been delivered yet.
     * Defaults to null in order to use the respective defaults of each provider (0 for iOS/APNs and 2419200 (4 weeks) for Android/FCM).
     */
    private ?int $ttl = null;

    /**
     * Timestamp since the UNIX epoch specifying when the message expires.
     * Same effect as ttl (ttl takes precedence over expiration).
     */
    private ?int $expiration = null;

    /**
     * The delivery priority of the message.
     * Specify "default" or omit this field to use the default priority on each platform ("normal" on Android and "high" on iOS).
     *
     * Supported: 'default', 'normal', 'high'.
     */
    private string $priority = 'default';

    /**
     * The subtitle to display in the notification below the title.
     *
     * iOS only.
     */
    private ?string $subtitle = null;

    /**
     * Play a sound when the recipient receives this notification.
     * Specify "default" to play the device's default notification sound, or omit this field to play no sound.
     * Custom sounds are not supported.
     *
     * iOS only.
     */
    private ?string $sound = null;

    /**
     * Number to display in the badge on the app icon.
     * Specify zero to clear the badge.
     *
     * iOS only.
     */
    private int $badge = 0;

    /**
     * ID of the Notification Channel through which to display this notification.
     * If an ID is specified but the corresponding channel does not exist on the device (i.e. has not yet been created by your app),
     * the notification will not be displayed to the user.
     *
     * Android only.
     */
    private ?string $channelId = null;

    /**
     * ID of the notification category that this notification is associated with.
     * Must be on at least SDK 41 or bare workflow.
     *
     * @see ExpoMessage::$https://docs.expo.dev/versions/latest/sdk/notifications/#managing-notification-categories-interactive-notifications Notification categories
     */
    private ?string $categoryId = null;

    /**
     * Specifies whether this notification can be intercepted by the client app.
     * In Expo Go, this defaults to true, and if you change that to false, you may experience issues.
     * In standalone and bare apps, this defaults to false.
     *
     * iOS only.
     */
    private bool $mutableContent = false;

    /**
     * Create a new ExpoMessage instance.
     */
    private function __construct(string $title, string $body)
    {
        $this->body = $body;
        $this->title = $title;
    }

    /**
     * Start creating a message with a given title and body.
     */
    public static function create(string $title = '', string $body = ''): self
    {
        return new self($title, $body);
    }

    /**
     * Set the number to display in the badge on the app icon.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$badge
     */
    public function badge(int $value): self
    {
        if ($value < 0) {
            throw new UnexpectedValueException('The badge must be greater than or equal to 0.');
        }

        $this->badge = $value;

        return $this;
    }

    /**
     * Set the message body to display in the notification.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$body
     */
    public function body(string $value): self
    {
        if (empty($value)) {
            throw new UnexpectedValueException('The body must not be empty.');
        }

        $this->body = $value;

        return $this;
    }

    /**
     * Set the ID of the notification category that this notification is associated with.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$categoryId
     */
    public function categoryId(string $value): self
    {
        if (empty($value)) {
            throw new UnexpectedValueException('The categoryId must not be empty.');
        }

        $this->categoryId = $value;

        return $this;
    }

    /**
     * Set the ID of the Notification Channel through which to display this notification.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$channelId
     */
    public function channelId(string $value): self
    {
        if (empty($value)) {
            throw new UnexpectedValueException('The channelId must not be empty.');
        }

        $this->channelId = $value;

        return $this;
    }

    /**
     * Set the JSON data for the message.
     *
     * @throws \JsonException
     *
     * @see ExpoMessage::$data
     */
    public function data(Arrayable|Jsonable|JsonSerializable|array $value): self
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if ($value instanceof Jsonable) {
            $value = $value->toJson(JSON_THROW_ON_ERROR);
        } else {
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }

        $this->data = $value;

        return $this;
    }

    /**
     * Set the delivery priority of the message to 'default'.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$priority
     */
    public function default(): self
    {
        $this->priority = __FUNCTION__;

        return $this;
    }

    /**
     * Set the expiration time of the message.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$expiration
     */
    public function expiration(DateTimeInterface|int $value): self
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->getTimestamp();
        }

        if ($value - time() <= 0) {
            throw new UnexpectedValueException('The expiration time must be in the future.');
        }

        $this->expiration = $value;

        return $this;
    }

    /**
     * Set the delivery priority of the message to 'high'.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$priority
     */
    public function high(): self
    {
        $this->priority = __FUNCTION__;

        return $this;
    }

    /**
     * Set whether the notification can be intercepted by the client app.
     *
     * @see ExpoMessage::$mutableContent
     */
    public function mutableContent(bool $value = true): self
    {
        $this->mutableContent = $value;

        return $this;
    }

    /**
     * Set the delivery priority of the message to 'normal'.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$priority
     */
    public function normal(): self
    {
        $this->priority = __FUNCTION__;

        return $this;
    }

    /**
     * Play a sound when the recipient receives the notification.
     *
     * @see ExpoMessage::$sound
     */
    public function playSound(): self
    {
        $this->sound = 'default';

        return $this;
    }

    /**
     * Set the delivery priority of the message, either 'default', 'normal' or 'high.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$priority
     */
    public function priority(string $value): self
    {
        $value = strtolower($value);

        if (! in_array($value, self::PRIORITIES)) {
            throw new UnexpectedValueException('The priority must be default, normal or high.');
        }

        $this->priority = $value;

        return $this;
    }

    /**
     * Set the subtitle to display in the notification below the title.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$subtitle
     */
    public function subtitle(string $value): self
    {
        if (empty($value)) {
            throw new UnexpectedValueException('The subtitle must not be empty.');
        }

        $this->subtitle = $value;

        return $this;
    }

    /**
     * Set the title to display in the notification.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$title
     */
    public function title(string $value): self
    {
        if (empty($value)) {
            throw new UnexpectedValueException('The title must not be empty.');
        }

        $this->title = $value;

        return $this;
    }

    /**
     * Set the number of seconds for which the message may be kept around for redelivery.
     *
     * @throws UnexpectedValueException
     *
     * @see ExpoMessage::$ttl
     */
    public function ttl(int $value): self
    {
        if ($value <= 0) {
            throw new UnexpectedValueException('The TTL must be greater than 0.');
        }

        $this->ttl = $value;

        return $this;
    }

    /**
     * Convert the ExpoMessage instance to its JSON representation.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the ExpoMessage instance as an array.
     */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), filled(...));
    }
}
