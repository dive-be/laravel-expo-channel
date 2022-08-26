# Expo Notifications Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dive-be/laravel-expo-channel.svg?style=flat-square)](https://packagist.org/packages/dive-be/laravel-expo-channel)

[Expo](https://docs.expo.dev/push-notifications/overview/) channel for pushing notifications to your React Native apps.

## Installation

You can install the package via composer:

```bash
composer require dive-be/laravel-expo-channel
```

### Additional Security (optional)

You can require any push notifications to be sent with an additional [Access Token](https://docs.expo.dev/push-notifications/sending-notifications/#additional-security) before Expo delivers them to your users.

If you want to make use of this additional security layer, add the following to your `config/services.php` file:

```php
'expo' => [
    'access_token' => env('EXPO_ACCESS_TOKEN'),
],
```

## Usage

WIP

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
