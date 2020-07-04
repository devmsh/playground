<?php

namespace Tests\Feature;

use App\Notifications\AccountActivated;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FCMNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_with_fcm()
    {
        $this->postJson('api/register', [
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'device_name' => 'test',
            'fcm_token' => 'test-token',
        ])->assertStatus(201);

        $this->assertDatabaseHas('users',[
            'name' => 'test',
            'email' => 'test@laravel.com',
            'fcm_token' => 'test-token',
        ]);
    }

    public function test_login()
    {
        factory(User::class)->create([
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => Hash::make('12345678'),
            'active' => 1,
            'fcm_token' => null,

        ]);

        $this->postJson('api/login', [
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'device_name' => 'test',
            'fcm_token' => 'test-token',
        ])->assertOk();

        $this->assertDatabaseHas('users',[
            'name' => 'test',
            'email' => 'test@laravel.com',
            'fcm_token' => 'test-token',
        ]);
    }

    public function test_can_send_fcm_notification()
    {
        Notification::fake();

        /** @var User $user */
        $user = factory(User::class)->create([
            'fcm_token' => 'test-token'
        ]);

        $user->notify(new AccountActivated());

        Notification::assertSentTo($user, AccountActivated::class);
    }
}
