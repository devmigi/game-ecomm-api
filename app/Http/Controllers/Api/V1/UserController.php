<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


class UserController extends ApiController
{

    /**
     * API for login
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid Credentials', [], 422);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->sendResponse(['token' => $token, 'user' => new UserResource($user)], 'Successfully logged in.');
    }


    /**
     * API for login with Social account
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function loginWithSociaAccount(Request $request, $provider){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'device_name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        // only google login supported check
        if(strtolower($provider) != 'google'){
            return $this->sendError($provider . ' - Not Supported');
        }

        try {
            $user = Socialite::driver('google')->userFromToken($request->token);

        } catch (\Exception $e) {
            return $this->sendError("Unable to verify {$provider} account" );
        }

        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            $token = $existingUser->createToken($request->device_name)->plainTextToken;
        } else {
            $newUser                    = new User();
//            $newUser->provider_name     = $provider;
//            $newUser->provider_id       = $user->getId();
            $newUser->name              = $user->getName();
            $newUser->email             = $user->getEmail();
            $newUser->password          = Hash::make(Str::random(40));
            $newUser->email_verified_at = now();
//            $newUser->avatar            = $user->getAvatar();
            $newUser->save();

            $token = $newUser->createToken($request->device_name)->plainTextToken;
            $existingUser = $newUser;
        }

        return $this->sendResponse(['token' => $token, 'user' => new UserResource($existingUser)], 'Successfully logged in.');
    }




    /**
     * API for user signup
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->sendResponse(['token' => $token, 'user' => new UserResource($user)], 'Successfully registered.');

    }


    /**
     * API for logout
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request){

        $loggedOut = $request->user()->currentAccessToken()->delete();

        if($loggedOut){
            return $this->sendSuccess('Successfully logged out.');
        }
        else{
            return $this->sendError('Something went wrong.');
        }
    }

    /**
     * Get logged in user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        return $this->sendResponse(new UserResource($request->user()), 'User Profile');
    }

}
