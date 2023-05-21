<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Update user's locale or sets a locale cookie and redirect back
     */
    public function set_locale(Request $request): RedirectResponse {
        $valid_locales = join(',', config('app.available_locales', ['en']));
        $validated = $request->validate(['locale' => "required|string|in:{$valid_locales}"]);

        if(auth()->check()) {
            $request->user()->update(['locale' => $validated['locale']]);
            return back();
        } else {
            $locale_cookie = cookie('locale', $validated['locale']);
            return back()->withCookie($locale_cookie);
        }
    }

    public function terms_and_conditions() {
        $locale = app()->getLocale();
        $file = resource_path("views/terms_privacy/{$locale}/terms-and-conditions.md");
        $html = Str::markdown(file_get_contents($file));
        return view('terms_privacy.terms_privacy', ['md' => $html, 'title' => __('Terms and Conditions')]);
    }
    
    public function privacy_policy() {
        $locale = app()->getLocale();
        $file = resource_path("views/terms_privacy/{$locale}/privacy-policy.md");
        $html = Str::markdown(file_get_contents($file));
        return view('terms_privacy.terms_privacy', ['md' => $html, 'title' => __('Privacy Policy')]);
    }
}
