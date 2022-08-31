<?php declare(strict_types=1);

namespace Tests\Unit;

use NotificationChannels\Expo\AsExpoPushToken;
use NotificationChannels\Expo\ExpoPushToken;
use NotificationChannels\Expo\ExpoPushTokenRule;
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
        $token = ExpoPushToken::make($value);

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

        ExpoPushToken::make($value);
    }

    /** @test */
    public function it_is_equatable()
    {
        $tokenA = ExpoPushToken::make('ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]');
        $tokenB = ExpoPushToken::make('ExponentPushToken[JQoRAH65GV7qZX8YUyx8Rn]');
        $tokenC = 'ExponentPushToken[JQoRAH65GV7qZX8YUyx8Rn]';

        $this->assertTrue($tokenA->equals($tokenA));
        $this->assertFalse($tokenA->equals($tokenB));
        $this->assertFalse($tokenA->equals($tokenC));
        $this->assertTrue($tokenB->equals($tokenC));
    }

    /** @test */
    public function it_is_castable()
    {
        $caster = ExpoPushToken::castUsing([]);

        $this->assertSame(AsExpoPushToken::class, $caster);
    }

    /** @test */
    public function it_can_be_validated()
    {
        $rule = ExpoPushToken::rule();

        $this->assertInstanceOf(ExpoPushTokenRule::class, $rule);
    }

    /** @test */
    public function it_is_stringable()
    {
        $token = ExpoPushToken::make($value = 'ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]');

        $this->assertSame($value, $token->asString());
        $this->assertSame($value, (string) $token);
    }
}
