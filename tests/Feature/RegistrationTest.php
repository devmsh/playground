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
}
