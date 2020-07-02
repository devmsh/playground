<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Http\Response;
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
        ])->assertStatus(Response::HTTP_CREATED);

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
        ])->assertStatus(Response::HTTP_BAD_REQUEST);

    }

    public function test_registration_by_mobile()
    {
        $this->postJson('api/register', [
            'name' => 'test',
            'mobile' => '+972599999999',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'test'
        ])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'test',
            "tokenable_type" => User::class,
            "tokenable_id" => 1,
        ]);
    }
}
