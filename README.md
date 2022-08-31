<p align="center"><img src="https://github.com/dive-be/laravel-expo-channel/blob/master/art/expo.png?raw=true" alt="Social Card of Laravel Expo Channel"></p>

# Expo Notifications Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dive-be/laravel-expo-channel.svg?style=flat-square)](https://packagist.org/packages/dive-be/laravel-expo-channel)

[Expo](https://docs.expo.dev/push-notifications/overview/) channel for pushing notifications to your React Native apps.

## Disclaimer

This package is not (yet) part of the [Laravel Notification Channels](https://laravel-notification-channels.com) project, because the maintainer seems to be inactive and the [existing expo channel](https://github.com/laravel-notification-channels/expo/issues/1) has never been completed and is pretty much abandoned. This package respects all of the project's conventions (namespace, message creation ...), so a possible migration in the future should just be about replacing the package's name in your `composer.json`.

## Installation

You can install the package via composer:

```bash
composer require dive-be/laravel-expo-channel
```

## Additional Security (optional)

You can require any push notifications to be sent with an additional [Access Token](https://docs.expo.dev/push-notifications/sending-notifications/#additional-security) before Expo delivers them to your users.

If you want to make use of this additional security layer, add the following to your `config/services.php` file:

```php
'expo' => [
    'access_token' => env('EXPO_ACCESS_TOKEN'),
],
```

## Usage

First things first, you need to have a [Notification](https://laravel.com/docs/9.x/notifications) that needs to be delivered to someone. Check out the [Laravel documentation](https://laravel.com/docs/9.x/notifications#generating-notifications) for more information on generating notifications. 

You can now use the `expo` channel in the `via()` method of the corresponding `Notification`.

### Notification / ExpoMessage

Detailed explanation regarding the Expo Message Request Format can be found [here](https://docs.expo.dev/push-notifications/sending-notifications/#message-request-format).

```php
final class SuspiciousActivityDetected extends Notification
{
    public function toExpo($notifiable): ExpoMessage
    {
        return ExpoMessage::create('Suspicious Activity')
            ->body('Someone tried logging in to your account!')
            ->data($notifiable->only('email', 'id'))
            ->expires(Carbon::now()->addHour())
            ->priority('high')
            ->playSound();
    }

    public function via($notifiable): array
    {
        return ['expo'];
    }
}
```

> `ExpoMessage` does not accept a recipent (to), because that's derived from the `Notifiable` instance!

### Notifiable / ExpoPushToken

Next, you will have to set a `routeNotificationForExpo()` method in your `Notifiable` model. 

#### Unicasting (single device)

The method **must** return an instance of `ExpoPushToken`, which you can easily achieve by defining it in the `$casts` array of your model:

```php
final class User extends Authenticatable
{
    use Notifiable;

    protected $casts = ['expo_token' => ExpoPushToken::class];

    public function routeNotificationForExpo(): ExpoPushToken
    {
        return $this->expo_token;
    }
}
```

#### Multicasting (multiple devices)

The method **must** return an `array<int, ExpoPushToken>` or `Collection<int, ExpoPushToken>`, the specific implementation depends on your use case. An example:

```php
final class User extends Authenticatable
{
    use Notifiable;

    /**
    * @return Collection<int, ExpoPushToken>
    */
    public function routeNotificationForExpo(): Collection
    {
        return $this->devices->pluck('expo_token');
    }
}
```

### Sending

Once everything is in place, you can simply send a notification by calling:

```php
$user->notify(new SuspiciousActivityDetected());
```

## Expo Message Request Format

The `ExpoMessage` class contains the following methods for defining the message payload. All of these methods correspond to the available payload defined in the [Expo Push documentation](https://docs.expo.dev/push-notifications/sending-notifications/#message-request-format). 

- [Badge (iOS)](#badge-ios)
- [Body](#body)
- [Category ID](#category-id)
- [Channel ID (Android)](#channel-id-android)
- [JSON data](#json-data)
- [Default priority](#default-priority)
- [Expiration](#expiration)
- [High priority](#high-priority)
- [Mutable content (iOS)](#mutable-content-ios)
- [Normal priority](#normal-priority)
- [Notification sound (iOS)](#notification-sound-ios)
- [Priority](#priority)
- [Subtitle (iOS)](#subtitle-ios)
- [Title](#title)
- [TTL (Time to live)](#ttl-time-to-live)

### Badge (iOS)

Sets the number to display in the badge on the app icon.

```php
badge(int $value)
```

> **Note**
> The value must be greater than or equal to 0.

### Body

Sets the message body to display in the notification.

```php
body(string $value)
```

> **Note**
> The value must not be empty.

### Category ID

Sets the ID of the notification category that this notification is associated with.

```php
categoryId(string $value)
```

> **Note**
> The value must not be empty.

### Channel ID (Android)

Sets the ID of the Notification Channel through which to display this notification.

```php
channelId(string $value)
```

> **Note**
> The value must not be empty.

### JSON data

Sets the JSON data for the message.

```php
data(Arrayable|Jsonable|JsonSerializable|array $value)
```

> **Warning**
> We're compressing JSON payloads that exceed 1 KiB using Gzip. While you could technically send more than 4 KiB of data, this is not recommended.

### Default priority

Sets the delivery priority of the message to `default`.

```php
default()
```

> **Note**
> Achieves the same result as `priority('default')`

### Expiration

Sets the expiration time of the message. Same effect as TTL.

```php
expires(DateTimeInterface|int $value)
```

> **Warning**
> `TTL` takes precedence if both are set.

> **Note**
> The value must be in the future.

### High priority

Sets the delivery priority of the message to 'high'.

```php
high()
```

> **Note**
> Achieves the same result as `priority('high')`

### Mutable content (iOS)

Sets whether the notification can be intercepted by the client app.

```php
mutableContent(bool $value = true)
```

### Normal priority

Sets the delivery priority of the message to `normal`.

```php
normal()
```

> **Note**
> Achieves the same result as `priority('normal')`

### Notification sound (iOS)

Play the default notification sound when the recipient receives the notification.

```php
playSound()
```

> **Warning**
> Custom sounds are not supported.

### Priority

Sets the delivery priority of the message.

```php
priority(string $value)
```

> **Note**
> The value must be `default`, `normal` or `high`.

### Subtitle (iOS)

Sets the subtitle to display in the notification below the title.

```php
subtitle(string $value)
```

> **Note**
> The value must not be empty.

### Title

Set the title to display in the notification.

```php
title(string $value)
```

> **Note**
> The title must not be empty.

### TTL (Time to live)

Set the number of seconds for which the message may be kept around for redelivery.

```php
ttl(int $value)
```

> **Warning**
> Takes precedence over `expiration` if both are set.

> **Note**
> The value must be greater than 0.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email oss@dive.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
