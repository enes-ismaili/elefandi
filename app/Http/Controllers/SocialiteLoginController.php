<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteLoginController extends Controller
{
	// use Socialite;
	
	/**
	 * Redirect the user to the provider authentication page.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function redirectToProvider($driver)
	{
		return Socialite::driver($driver)->redirect();
	}
	
    /**
	 * Obtain the user information from provider.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function handleProviderCallback($driver)
	{
		try {
			$user = Socialite::driver($driver)->user();
		} catch (\Exception $e) {
			return redirect()->route('login');
		}


		$existingUser = User::where('email', $user->getEmail())->first();

		if ($existingUser) {
			auth()->login($existingUser, true);
		} else {
			$firstName = '';
			$lastName = '';
			if($user->user && isset($user->user['given_name'])){
				$firstName = $user->user['given_name'];
				$lastName = $user->user['family_name'];
			} else {
				$fullName = explode(' ', $user->getName());
				if(count($fullName) > 1){
					$firstName = $fullName[0];
					$lastName = $fullName[1];
				}
			}
			$newUser                    = new User;
			$newUser->provider_name     = $driver;
			$newUser->provider_id       = $user->getId();
			$newUser->first_name        = $firstName;
			$newUser->last_name         = $lastName;
			$newUser->email             = $user->getEmail();
			// we set email_verified_at because the user's email is already veridied by social login portal
			$newUser->email_verified_at = now();
			$newUser->password         	= Hash::make('ElefandiSocialLogin55@');
			$newUser->country_id = 1;
			$newUser->role = 1;
			$newUser->status = 1;
			$newUser->save();

			auth()->login($newUser, true);
		}

		return redirect(route('home'));
	}
}
