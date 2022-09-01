<?php declare(strict_types=1);

namespace Tests\Unit;

use NotificationChannels\Expo\Gateway\ExpoErrorType;
use PHPUnit\Framework\TestCase;

final class ExpoErrorTypeTest extends TestCase
{
    /** @test */
    public function it_is_assertable()
    {
        $type = ExpoErrorType::MessageTooBig;

        $this->assertTrue($type->isMessageTooBig());
        $this->assertFalse($type->isDeviceNotRegistered());
    }

    /**
     * @dataProvider errors
     * @test
     */
    public function it_can_be_instantiated_using_the_backed_values(string $error)
    {
        $instance = ExpoErrorType::from($error);

        $this->assertSame($error, $instance->value);
    }

    protected function errors(): array
    {
        return [
            ['DeviceNotRegistered'],
            ['MessageTooBig'],
            ['MessageRateExceeded'],
            ['MismatchSenderId'],
            ['InvalidCredentials'],
        ];
    }
}
