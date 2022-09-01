<?php declare(strict_types=1);

namespace Tests\Unit;

use NotificationChannels\Expo\Gateway\ExpoResponse;
use PHPUnit\Framework\TestCase;

final class ExpoResponseTest extends TestCase
{
    /** @test */
    public function it_can_create_a_failure_response()
    {
        $response = ExpoResponse::failed($errors = ['status' => 500]);

        $this->assertTrue($response->isFailure());
        $this->assertFalse($response->isFatal());
        $this->assertFalse($response->isOk());
        $this->assertSame($errors, $response->errors());
        $this->assertSame('', $response->message());
    }

    /** @test */
    public function it_can_create_a_fatal_response()
    {
        $response = ExpoResponse::fatal($message = 'Something went horribly wrong.');

        $this->assertTrue($response->isFatal());
        $this->assertFalse($response->isFailure());
        $this->assertFalse($response->isOk());
        $this->assertSame($message, $response->message());
        $this->assertSame([], $response->errors());
    }

    /** @test */
    public function it_can_create_an_ok_response()
    {
        $response = ExpoResponse::ok();

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isFailure());
        $this->assertFalse($response->isFatal());
        $this->assertSame('', $response->message());
        $this->assertSame([], $response->errors());
    }
}
