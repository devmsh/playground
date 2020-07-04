<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login()
    {
        factory(User::class)->create([
            'name' => 'test',
            'email' => 'test@laravel.com',
            'password' => Hash::make('12345678'),
            'active' => 1,
        ]);

        $this->postJson('api/login', [
            'email' => 'test@laravel.com',
            'password' => '12345678',
            'device_name' => 'test'
        ])->assertOk();
    }

    public function test_login_by_mobile()
    {
        factory(User::class)->create([
            'name' => 'test',
            'mobile' => '+972599999999',
            'password' => Hash::make('12345678'),
            'active' => 1,
        ]);

        $this->postJson('api/login', [
            'mobile' => '+972599999999',
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
        ])->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }

    public function test_login_attemps_by_mobile()
    {
        factory(User::class)->create([
            'name' => 'test',
            'mobile' => '+972599999999',
            'password' => Hash::make('12345678'),
        ]);

        foreach (range(1, 3) as $x) {
            $this->postJson('api/login', [
                'mobile' => '+972599999999',
                'password' => '123456781',
                'device_name' => 'test'
            ]);
        }

        $this->postJson('api/login', [
            'mobile' => '+972599999999',
            'password' => '12345678',
            'device_name' => 'test'
        ])->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
