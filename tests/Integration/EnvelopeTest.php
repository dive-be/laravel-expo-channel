<?php declare(strict_types=1);

namespace Tests\Integration;

use NotificationChannels\Expo\ExpoEnvelope;
use NotificationChannels\Expo\ExpoMessage;
use NotificationChannels\Expo\ExpoPushToken;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class EnvelopeTest extends TestCase
{
    /** @test */
    public function it_can_create_an_instance()
    {
        $envelope = ExpoEnvelope::create($this->recipients(), $this->message());

        $this->assertInstanceOf(ExpoEnvelope::class, $envelope);
    }

    /** @test */
    public function it_doesnt_allow_creation_if_there_are_no_recipients()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('There must be at least 1 recipient.');

        ExpoEnvelope::create([], $this->message());
    }

    /** @test */
    public function it_is_arrayable_and_jsonable()
    {
        $envelope = ExpoEnvelope::create($this->recipients(), $this->message());

        $array = $envelope->toArray();

        $this->assertSame($data = [
            'title' => 'iOS',
            'body' => 'Android',
            'priority' => 'default',
            'sound' => 'default',
            'badge' => 1337,
            'mutableContent' => false,
            'to' => ['ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]'],
        ], $array);

        $this->assertSame(json_encode($data), $envelope->toJson());
    }

    private function recipients(): array
    {
        return [ExpoPushToken::fromString('ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]')];
    }

    private function message(): ExpoMessage
    {
        return ExpoMessage::create('iOS', 'Android')
            ->playSound()
            ->badge(1337);
    }
}
