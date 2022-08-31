<?php declare(strict_types=1);

namespace Tests\Integration;

use Illuminate\Database\Eloquent\Model;
use NotificationChannels\Expo\AsExpoPushToken;
use NotificationChannels\Expo\ExpoPushToken;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use UnexpectedValueException;

final class CastingTest extends TestCase
{
    /** @test */
    public function it_can_get_an_attribute_as_an_expo_push_token()
    {
        $user = new User(['expo_token' => $token = 'ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]']);
        $notifiable = new Notifiable(['expo_token' => $token]);

        $this->assertInstanceOf(ExpoPushToken::class, $user->expo_token);
        $this->assertInstanceOf(ExpoPushToken::class, $notifiable->expo_token);
        $this->assertEquals($token, $user->expo_token);
        $this->assertEquals($token, $notifiable->expo_token);
    }

    /** @test */
    public function it_can_set_an_expo_push_token_on_an_attribute()
    {
        $user = new User();
        $notifiable = new Notifiable();

        $user->expo_token = $token = 'ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]';
        $notifiable->expo_token = $token;

        $this->assertInstanceOf(ExpoPushToken::class, $user->expo_token);
        $this->assertInstanceOf(ExpoPushToken::class, $notifiable->expo_token);
        $this->assertEquals($token, $user->expo_token);
        $this->assertEquals($token, $notifiable->expo_token);
    }

    /** @test */
    public function it_ignores_nulls()
    {
        $user = new User(['expo_token' => null]);

        $this->assertNull($user->expo_token);
    }

    /** @test */
    public function it_disallows_invalid_expo_push_tokens()
    {
        $this->expectException(UnexpectedValueException::class);

        new User(['expo_token' => 'blablabla']);
    }

    /** @test */
    public function it_disallows_invalid_data_types()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectDeprecationMessage('The given value cannot be cast to an instance of ExpoPushToken.');

        new User(['expo_token' => 12345]);
    }
}

final class User extends Model
{
    protected $casts = ['expo_token' => AsExpoPushToken::class];
    protected $guarded = [];
}

final class Notifiable extends Model
{
    protected $casts = ['expo_token' => ExpoPushToken::class];
    protected $guarded = [];
}
