<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.api');
    }

    /**
     * The user has been registered.
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function registered(Request $request, User $user)
    {
        $token = $user->createToken($request->device_name);

        return new Response([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 201);
    }

    /**
     * Get a validator for an incoming registration request.
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, array_merge([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => 'required'
        ], $this->usernameRules($data)));
    }

    /**
     * Create a new user instance after a valid registration.
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Return the list of validation rules as specified in the lock config
     * @param array $data
     * @return array
     */
    protected function usernameRules(array $data): array
    {
        $fields = array_intersect(array_keys($data), config('lock.username_fields'));

        $username = array_pop($fields);

        return [
            $username => config('lock.username_registration_validation')[$username]
        ];
    }
}
