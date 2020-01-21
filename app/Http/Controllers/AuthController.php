<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use URL;


class AuthController extends Controller
{

    use SendsPasswordResetEmails;

    public function login (Request $request) {

        $response_type = $request->format('json');

//        dd($response_type);

        $user = User::where('email', $request->email)->first();

        if ($user) {

            if (Hash::check($request->password, $user->password)) {

                $token = $user->createToken('MHSRV Notification token for now ' . time())->accessToken;

                $response = ['status'=>'success','message'=>'Successfully LoggedIn!','token' => $token,'user_info' => $user];
                return response()->$response_type($response, 200);
            } else {
                $response = ['status'=>'error','message'=>'Password mismatch!'];
                return response()->$response_type($response, 422);
            }

        } else {
            $response = ['status'=>'error','message'=>'User does not exist!'];
            return response()->$response_type($response, 404);
        }

    }

    public function logout (Request $request) {
    	$response_type = $request->format('json');

        $token = $request->user()->token();
        $token->revoke();

        $response = ['status'=>'success','message'=>'successfully logged out!'];
        return response()->$response_type($response, 200);

    }

    /**
     *  social login function
     */

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }


    public function handleProviderCallback($provider)
    {
//        dd($provider);
        // this user we get back is not our user model, but a special user object that has all the information we need
        $providerUser = Socialite::driver($provider)->stateless()->user();

        // we have successfully authenticated via facebook at this point and can use the provider user to log us in.

        // for example we might do something like... Check if a user exists with the email and if so, log them in.
        $user = User::query()->firstOrNew(['email' => $providerUser->getEmail()]);

        // maybe we can set all the values on our user model if it is new... right now we only have name
        // but you could set other things like avatar or gender
        if (!$user->exists) {
            $user->name = $providerUser->getName();
            $user->save();
        }

        /**
         * At this point we done.  You can use whatever you are using for authentication here...
         * for example you might do something like this if you were using JWT
         */

        $token = JWTAuth::fromUser($user);

        return new JsonResponse([
            'token' => $token
        ]);
    }

    public function social_login(Request $request)
    {
        $provider = $request->input('provider');
        switch($provider){
            case 'facebook':
                $social_user = Socialite::driver('facebook')->fields([
                    'name',
                    'first_name',
                    'last_name',
                    'email'
                ]);
                break;
            case 'google':
                $social_user = Socialite::driver('google')
                    ->scopes(['profile','email']);
                break;
            default :
                $social_user = null;
        }

        abort_if($social_user == null , 422,'Provider missing');

        $social_user_details = $social_user->userFromToken($request->input('access_token'));

        abort_if($social_user_details == null , 400,'Invalid credentials'); //|| $fb_user->id != $request->input('userID')

        $account = User::where("provider_user_id",$social_user_details->id)
            ->where("provider",$provider)
            ->with('user')->first();

        if($account){
            return $this->issueToken($account->user);
        }
        else {
            // create new user and social login if user with social id not found.
            $user = User::where("email",$social_user_details->getEmail())->first();
            if(!$user){
                // create new social login if user already exist.
                $user = new User;
                switch($provider){
                    case 'facebook':
                        $user->first_name = $social_user_details->user['first_name'];
                        $user->last_name = $social_user_details->user['last_name'];
                        break;
                    case 'google':
                        $user->first_name = $social_user_details->user['name']['givenName'];
                        $user->last_name = $social_user_details->user['name']['familyName'];
                        break;
                    default :
                }

                $user->name = $social_user_details->user['first_name'] . ' ' . $social_user_details->user['last_name'];;
                $user->email = $social_user_details->getEmail();
                $user->password = Hash::make('social');
                $user->provider = $provider;
                $user->provider_id = $social_user_details->id;
                $user->save();
            }

            return $this->issueToken($user);
        }

    }

    private function issueToken(User $user) {

        $userToken = $user->token() ?? $user->createToken('socialLogin');

        return [
            "token_type" => "Bearer",
            "access_token" => $userToken->accessToken
        ];
    }

    public function callback($provider)
    {

        $getInfo = Socialite::driver($provider)->user();

        dd($getInfo);

        $user = $this->createUser($getInfo,$provider);

        auth()->login($user);

        return redirect()->to('/home');

    }

    function createUser($getInfo,$provider){

        $user = User::where('provider_id', $getInfo->id)->first();

        if (!$user) {
            $user = User::create([
                'name'          => $getInfo->name,
                'first_name'    => explode(' ', $getInfo->name)[0],
                'email'         => $getInfo->email,
                'provider'      => $provider,
                'provider_id'   => $getInfo->id
            ]);
        }
        return $user;
    }
}
