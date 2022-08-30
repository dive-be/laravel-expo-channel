<?php declare(strict_types=1);

namespace Tests\Unit;

use NotificationChannels\Expo\AsExpoPushToken;
use NotificationChannels\Expo\ExpoPushToken;
use PHPUnit\Framework\TestCase;
use Tests\ExpoTokensDataset;
use UnexpectedValueException;

final class ExpoPushTokenTest extends TestCase
{
    use ExpoTokensDataset;

    /**
     * @dataProvider valid
     * @test
     */
    public function it_can_create_an_instance(string $value)
    {
        $token = ExpoPushToken::fromString($value);

        $this->assertSame($value, $token->asString());
    }

    /**
     * @dataProvider invalid
     * @test
     */
    public function it_doesnt_allow_invalid_tokens(string $value)
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("{$value} is not a valid push token.");

        ExpoPushToken::fromString($value);
    }

    /** @test */
    public function it_is_castable()
    {
        $caster = ExpoPushToken::castUsing([]);

        $this->assertSame(AsExpoPushToken::class, $caster);
    }

    /** @test */
    public function it_is_stringable()
    {
        $token = ExpoPushToken::fromString($value = 'ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]');

        $this->assertSame($value, $token->asString());
        $this->assertSame($value, (string) $token);
    }
}
