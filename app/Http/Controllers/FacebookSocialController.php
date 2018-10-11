<?php

namespace App\Http\Controllers;

use App\User;
use Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacebookSocialController extends Controller
{

    /**
     * Since facebook will give you name, email, gender by default,
     * You'll only need to initialize Facebook scopes after getting permission
     */
    /* const facebookScope = [
        'user_birthday',
        'user_location',
    ]; */


    /**
     * Initialize Facebook fields to override
     */
    const facebookFields = [
        'name', // Default
        'email', // Default
        'gender', // Default
        //'birthday', // I've given permission
        //'location', // I've given permission
    ];



    /**
     * Redirect the user to the facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->fields(self::facebookFields)
                                            //->scopes(self::facebookScope)
                                            ->redirect();
        //return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $fbUser = Socialite::driver('facebook')->fields(self::facebookFields)->user();

        $findUser = User::where('email', $fbUser->email)->first();

        if ($findUser) {

            Auth::login($findUser);
            return redirect(route('home'));

        } else {

            $user = new User;
            $user->name = $fbUser->name;
            $user->email = $fbUser->email;
            $user->password = bcrypt(123456);
            $user->save();

            Auth::login($user);
            return redirect(route('home'));
        }

        // $user->token;
    }
}
