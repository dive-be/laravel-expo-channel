<?php declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Carbon;
use JsonSerializable;
use NotificationChannels\Expo\ExpoMessage;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class ExpoMessageTest extends TestCase
{
    /** @test */
    public function it_can_be_constructed_with_a_title_and_a_body()
    {
        $msg = ExpoMessage::create('John', 'Cena');

        ['body' => $body, 'title' => $title] = $msg->toArray();

        $this->assertSame('John', $title);
        $this->assertSame('Cena', $body);
    }

    /** @test */
    public function it_can_set_a_badge()
    {
        $msg = ExpoMessage::create()->badge($value = 1337);

        ['badge' => $badge] = $msg->toArray();

        $this->assertSame($value, $badge);
    }

    /** @test */
    public function it_doesnt_allow_a_badge_below_zero()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The badge must be greater than or equal to 0.');

        ExpoMessage::create()->badge(-1337);
    }

    /** @test */
    public function it_can_set_a_body()
    {
        $msg = ExpoMessage::create()->body($value = 'Laravel, Framework');

        ['body' => $body] = $msg->toArray();

        $this->assertSame($value, $body);
    }

    /** @test */
    public function it_doesnt_allow_an_empty_body()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The body must not be empty.');

        ExpoMessage::create()->body('');
    }

    /** @test */
    public function it_can_set_a_category_id()
    {
        $msg = ExpoMessage::create()->categoryId($value = 'Laravel');

        ['categoryId' => $categoryId] = $msg->toArray();

        $this->assertSame($value, $categoryId);
    }

    /** @test */
    public function it_doesnt_allow_an_empty_category_id()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The categoryId must not be empty.');

        ExpoMessage::create()->categoryId('');
    }

    /** @test */
    public function it_can_set_a_channel_id()
    {
        $msg = ExpoMessage::create()->channelId($value = 'Laravel');

        ['channelId' => $channelId] = $msg->toArray();

        $this->assertSame($value, $channelId);
    }

    /** @test */
    public function it_doesnt_allow_an_empty_channel_id()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The channelId must not be empty.');

        ExpoMessage::create()->channelId('');
    }

    /** @test */
    public function it_can_set_the_json_data()
    {
        $msgA = ExpoMessage::create()->data($value = ['laravel' => 'framework']);
        $msgB = ExpoMessage::create()->data(new TestArrayable());
        $msgC = ExpoMessage::create()->data(new TestJsonable());
        $msgD = ExpoMessage::create()->data(new TestJsonSerializable());

        ['data' => $dataA] = $msgA->toArray();
        ['data' => $dataB] = $msgB->toArray();
        ['data' => $dataC] = $msgC->toArray();
        ['data' => $dataD] = $msgD->toArray();

        $this->assertEquals($data = json_encode($value), $dataA);
        $this->assertEquals($data, $dataB);
        $this->assertEquals($data, $dataC);
        $this->assertEquals($data, $dataD);
    }

    /** @test */
    public function it_can_set_the_priority_to_default()
    {
        $msgA = ExpoMessage::create()->default();
        $msgB = ExpoMessage::create()->priority('default');

        ['priority' => $priorityA] = $msgA->toArray();
        ['priority' => $priorityB] = $msgB->toArray();

        $this->assertSame($priority = 'default', $priorityA);
        $this->assertSame($priority, $priorityB);
    }

    /** @test */
    public function it_can_set_an_expiration()
    {
        $msgA = ExpoMessage::create()->expiration($expiration = time() + 60);
        $msgB = ExpoMessage::create()->expiration(Carbon::now()->addSeconds(60));

        ['expiration' => $expirationA] = $msgA->toArray();
        ['expiration' => $expirationB] = $msgB->toArray();

        $this->assertSame($expiration, $expirationA);
        $this->assertSame($expiration, $expirationB);
    }

    /** @test */
    public function it_doesnt_allow_an_expiration_in_the_past()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The expiration time must be in the future.');

        ExpoMessage::create()->expiration(time() - 60);
    }

    /** @test */
    public function it_can_set_the_priority_to_high()
    {
        $msgA = ExpoMessage::create()->high();
        $msgB = ExpoMessage::create()->priority('high');

        ['priority' => $priorityA] = $msgA->toArray();
        ['priority' => $priorityB] = $msgB->toArray();

        $this->assertSame($priority = 'high', $priorityA);
        $this->assertSame($priority, $priorityB);
    }

    /** @test */
    public function it_can_set_the_mutable_content()
    {
        $msg = ExpoMessage::create()->mutableContent();

        ['mutableContent' => $mutableContent] = $msg->toArray();

        $this->assertTrue($mutableContent);
    }

    /** @test */
    public function it_can_set_the_priority_to_normal()
    {
        $msgA = ExpoMessage::create()->normal();
        $msgB = ExpoMessage::create()->priority('normal');

        ['priority' => $priorityA] = $msgA->toArray();
        ['priority' => $priorityB] = $msgB->toArray();

        $this->assertSame($priority = 'normal', $priorityA);
        $this->assertSame($priority, $priorityB);
    }

    /** @test */
    public function it_can_play_a_sound()
    {
        $msg = ExpoMessage::create()->playSound();

        ['sound' => $sound] = $msg->toArray();

        $this->assertSame('default', $sound);
    }

    /** @test */
    public function it_can_set_a_priority()
    {
        $msg = ExpoMessage::create()->priority('HIGH');

        ['priority' => $priority] = $msg->toArray();

        $this->assertSame('high', $priority);
    }

    /** @test */
    public function it_doesnt_allow_an_invalid_priority()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The priority must be default, normal or high.');

        ExpoMessage::create()->priority('extreme');
    }

    /** @test */
    public function it_can_set_a_subtitle()
    {
        $msg = ExpoMessage::create()->subtitle($value = "You can't see me");

        ['subtitle' => $subtitle] = $msg->toArray();

        $this->assertSame($value, $subtitle);
    }

    /** @test */
    public function it_doesnt_allow_an_empty_subtitle()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The subtitle must not be empty.');

        ExpoMessage::create()->subtitle('');
    }

    /** @test */
    public function it_can_set_a_title()
    {
        $msg = ExpoMessage::create()->title($value = "You can't see me");

        ['title' => $title] = $msg->toArray();

        $this->assertSame($value, $title);
    }

    /** @test */
    public function it_doesnt_allow_an_empty_title()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The title must not be empty.');

        ExpoMessage::create()->title('');
    }

    /** @test */
    public function it_can_set_a_ttl()
    {
        $msg = ExpoMessage::create()->ttl($value = 60);

        ['ttl' => $ttl] = $msg->toArray();

        $this->assertSame($value, $ttl);
    }

    /** @test */
    public function it_doesnt_allow_zero_or_a_negative_ttl()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The TTL must be greater than 0.');

        ExpoMessage::create()->ttl(0);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The TTL must be greater than 0.');

        ExpoMessage::create()->ttl(-60);
    }

    /** @test */
    public function it_is_arrayable_and_json_serializable()
    {
        $msg = ExpoMessage::create('Exponent', 'Firebase Cloud Messaging')
            ->badge(3)
            ->playSound()
            ->ttl(120)
            ->high();

        $arrayable = $msg->toArray();
        $jsonSerializable = $msg->jsonSerialize();

        $this->assertSame($data = [
            'title' => 'Exponent',
            'body' => 'Firebase Cloud Messaging',
            'ttl' => 120,
            'priority' => 'high',
            'sound' => 'default',
            'badge' => 3,
            'mutableContent' => false,
        ], $arrayable);

        $this->assertSame($data, $jsonSerializable);
    }
}

class TestArrayable implements Arrayable
{
    public function toArray(): array
    {
        return ['laravel' => 'framework'];
    }
}

class TestJsonable implements Jsonable
{
    public function toJson($options = 0): string
    {
        return json_encode(['laravel' => 'framework'], $options);
    }
}

class TestJsonSerializable implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return ['laravel' => 'framework'];
    }
}
