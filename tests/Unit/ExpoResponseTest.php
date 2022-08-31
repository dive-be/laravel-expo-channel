<?php declare(strict_types=1);

namespace Tests\Unit;

use NotificationChannels\Expo\ExpoResponse;
use PHPUnit\Framework\TestCase;

final class ExpoResponseTest extends TestCase
{
    /** @test */
    public function it_can_create_a_failure_response()
    {
        $response = ExpoResponse::failure($errors = ['status' => 500]);

        $this->assertTrue($response->failure);
        $this->assertSame($errors, $response->errors);
    }

    /** @test */
    public function it_can_create_an_ok_response()
    {
        $response = ExpoResponse::ok();

        $this->assertFalse($response->failure);
        $this->assertSame([], $response->errors);
    }
}
