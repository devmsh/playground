<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
     * Handle a login request to the application.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        if ($this->isAnonymousLogin()) {
            Auth::login(User::create([
                'name' => 'Anonymous user',
                'password' => Hash::make(Str::random()),
            ]));

            return $this->sendLoginResponse($request);
        }

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     * @param \Illuminate\Http\Request $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
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

        if ($request->fcm_token) {
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);
        }

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
        if ($this->isAnonymousLogin()) return "";

        $username = array_intersect(array_keys(request()->all()), config('lock.username_fields'));

        return array_pop($username) ?? "email";
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), [
            'active' => 1
        ]);
    }

    protected function isAnonymousLogin(): bool
    {
        return config('lock.anonymous_login')
            && request('type') === 'anonymous';
    }
}
