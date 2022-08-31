<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoError;
use NotificationChannels\Expo\ExpoErrorType;
use NotificationChannels\Expo\ExpoPushToken;
use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $error = ExpoError::make(
            ExpoPushToken::make('ExpoPushToken[abcdefgh]'),
            ExpoErrorType::InvalidCredentials,
            'The credentials are invalid'
        );

        $this->assertTrue($error->token->equals('ExpoPushToken[abcdefgh]'));
        $this->assertTrue($error->type->isInvalidCredentials());
        $this->assertSame('The credentials are invalid', $error->message);
    }
}
