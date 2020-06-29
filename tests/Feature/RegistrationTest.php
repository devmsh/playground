<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration()
    {
        $this->postJson('api/register', [
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'test'
        ])->assertStatus(201);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'test',
            "tokenable_type" => User::class,
            "tokenable_id" => 1,
        ]);
    }

    public function test_auth()
    {
        $user = factory(User::class)->create();
        $token = $user->createToken('token-name');

        $this->getJson('api/user', [
            "Authorization" => "Bearer {$token->plainTextToken}"
        ])->assertOk();
    }

    public function test_guest_api_middleware()
    {
        $user = factory(User::class)->create([
            "name" => "hi"
        ]);

        Sanctum::actingAs($user);
        $this->postJson('api/register', [
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'test'
        ])->assertStatus(400);

    }

    public function test_login()
    {
        factory(User::class)->create([
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => Hash::make('12345678'),
            'active' => 1,
        ]);

        $this->withoutExceptionHandling();
        $this->postJson('api/login', [
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'device_name' => 'test'
        ])->assertOk();
    }

    public function test_login_attemps()
    {
        factory(User::class)->create([
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => Hash::make('12345678'),
        ]);

        foreach (range(1, 3) as $x) {
            $this->postJson('api/login', [
                'email' => 'test@laravel.com',
                'password' => '123456781',
                'device_name' => 'test'
            ]);
        }

        $this->postJson('api/login', [
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'device_name' => 'test'
        ])->dump()->assertStatus(429);
    }

    public function test_logout()
    {
        $user = factory(User::class)->create([
            "name" => "hi"
        ]);
        $user->createToken('device1');

        $this->assertEquals(1, $user->tokens()->count());

        Sanctum::actingAs($user);
        $this->postJson('api/logout', [
            'device_name' => 'device1'
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
        $user->createToken('device1');
        $user->createToken('device2');
        $user->createToken('device3');

        $this->assertEquals(3, $user->tokens()->count());

        Sanctum::actingAs($user);
        $this->postJson('api/logout', [
            'device_name' => 'device1',
            'other_devices' => true,
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
        $user->createToken('device1');
        $user->createToken('device2');
        $user->createToken('device3');

        $this->assertEquals(3, $user->tokens()->count());

        Sanctum::actingAs($user);
        $this->postJson('api/logout', [])->assertOk()
            ->assertJson([
                "message" => "Successfully logged out from 3 device(s)"
            ]);

        $this->assertEquals(0, $user->tokens()->count());
    }
}
