<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnonymousLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_login()
    {
        $this->postJson('api/login', [
            'device_name' => 'test',
            'type' => 'anonymous'
        ])->assertOk();
    }

    public function test_disable_anonymous_login()
    {
        Config::set('lock.anonymous_login',false);

        $this->postJson('api/login', [
            'device_name' => 'test',
            'type' => 'anonymous'
        ])->dump()->assertOk();
    }
}
