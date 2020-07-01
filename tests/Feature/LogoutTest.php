<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout()
    {
        $user = factory(User::class)->create([
            "name" => "hi"
        ]);
        $token = $user->createToken('device1');

        $this->assertEquals(1, $user->tokens()->count());

        $this->postJson('api/logout', [],[
            "Authorization" => "Bearer {$token->plainTextToken}"
        ])->assertOk()
            ->assertJson([
                "message" => "Successfully logged out from 1 device(s)"
            ]);

        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_logout_from_other_devices()
    {
        $user = factory(User::class)->create([
            "name" => "hi"
        ]);
        $token = $user->createToken('device1');
        $user->createToken('device2');
        $user->createToken('device3');

        $this->assertEquals(3, $user->tokens()->count());

        $this->postJson('api/logout', [
            'from_other' => true,
        ],[
            "Authorization" => "Bearer {$token->plainTextToken}"
        ])->assertOk()
            ->assertJson([
                "message" => "Successfully logged out from 2 device(s)"
            ]);

        $this->assertEquals(1, $user->tokens()->count());
    }

    public function test_logout_all()
    {
        $user = factory(User::class)->create([
            "name" => "hi"
        ]);
        $token = $user->createToken('device1');
        $user->createToken('device2');
        $user->createToken('device3');

        $this->assertEquals(3, $user->tokens()->count());

        $this->withoutExceptionHandling();
        $this->postJson('api/logout', [
            'from_all' => true,
        ],[
            "Authorization" => "Bearer {$token->plainTextToken}"
        ])->assertOk()
            ->assertJson([
                "message" => "Successfully logged out from 3 device(s)"
            ]);

        $this->assertEquals(0, $user->tokens()->count());
    }
}
