<?php declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\Request;
use NotificationChannels\Expo\ExpoPushToken;
use Tests\FeatureTest;

final class ValidationTest extends FeatureTest
{
    protected function defineWebRoutes($router)
    {
        $router->post('push-tokens', static function (Request $request) {
            $request->validate(['token' => ['required', ExpoPushToken::rule()]]);

            return ['message' => 'ok'];
        });
    }

    /** @test */
    public function test_string_validation()
    {
        $token = 123456789;

        $response = $this->postJson('push-tokens', ['token' => $token]);

        $response->assertJsonValidationErrorFor('token');
        $this->assertSame($response->json('message'), 'The token must be a string.');
    }

    /** @test */
    public function test_format_validation()
    {
        $token = 'ExpoPushToken[]';

        $response = $this->postJson('push-tokens', ['token' => $token]);

        $response->assertJsonValidationErrorFor('token');
        $this->assertSame($response->json('message'), 'The token format is invalid.');
    }

    /** @test */
    public function test_happy_path()
    {
        $token = 'ExpoPushToken[GO3iMZEnfkqSsOEPWG9NWv]';

        $response = $this->postJson('push-tokens', ['token' => $token]);

        $response->assertOk();
    }
}
