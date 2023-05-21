<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'terms' => 'accepted'
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'locale' => $this->getLocale(),
        ]);
    }

    /**
     * Get the locale from the cookie or Accept-Language header
     * @return string The user locale, or 'en' if its not available
     */
    private function getLocale(): string {
        $locale = 'en';

        if($cookie = request()->cookie('locale')) {
            $locale = $cookie;
        } elseif($prefered = request()->getPreferredLanguage()) {
            $locale = explode('_', $prefered)[0];
        }

        $valid_locales = join(',', config('app.available_locales', ['en']));
        $validator = Validator::make(['locale' => $locale], [
            'locale' => "string|in:{$valid_locales}"
        ]);

        if(!$validator->passes()) $locale = 'en';

        return $locale;
    }
}
