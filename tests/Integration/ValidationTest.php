<?php declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use NotificationChannels\Expo\ExpoPushTokenRule;
use PHPUnit\Framework\TestCase;
use Tests\ExpoTokensDataset;

class ValidationTest extends TestCase
{
    use ExpoTokensDataset;

    /**
     * @dataProvider invalid
     * @test
     */
    public function it_fails(string $token)
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("{$token} is not a valid push token.");

        $validator = new Validator($this->trans(), compact('token'), ['token' => ExpoPushTokenRule::make()]);
        $validator->validate();
    }

    /**
     * @dataProvider valid
     * @test
     */
    public function it_passes(string $token)
    {
        $validator = new Validator($this->trans(), compact('token'), ['token' => ExpoPushTokenRule::make()]);

        $this->assertTrue($validator->passes());
    }

    private function trans(): Translator
    {
        return new Translator(new ArrayLoader(), 'en');
    }
}
