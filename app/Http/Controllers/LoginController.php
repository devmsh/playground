<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public int $maxAttempts = 3;
    public int $decayMinutes = 24;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.api')->except('logout');
    }

    /**
     * Send the response after the user was authenticated.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $token = $user->createToken($request->device_name);

        return new Response([
            'user' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Log the user out of the application.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }

    /**
     * The user has logged out of the application.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        $tokensCount = $this->removeTokens($request);

        return new Response([
            "message" => "Successfully logged out from {$tokensCount} device(s)"
        ]);
    }

    protected function removeTokens(Request $request): int
    {
        $currentToken = Auth::user()->currentAccessToken();

        $tokens = Auth::user()->tokens();

        if ($request->from_all) {
            return $tokens->delete();
        }

        if ($request->from_other) {
            return $tokens
                ->where('id', '!=', $currentToken->id)
                ->delete();
        }

        return $currentToken->delete();
    }

    public function username()
    {
        $username = array_intersect(array_keys(request()->all()), config('lock.username_fields'));

        return array_pop($username);
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), [
            'active' => 1
        ]);
    }
}
