<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'en';

        if(auth()->check()) {
            $locale = auth()->user()->locale;
            cookie()->forget('locale');
        } elseif($cookie = $request->cookie('locale')) {
            $locale = $cookie;
        } elseif($browser_language = $request->getPreferredLanguage()) {
            $locale = explode('_', $browser_language)[0];
        }
        
        $valid_locales = join(',', config('app.available_locales', ['en']));
        $validator = Validator::make(['locale' => $locale], [
            'locale' => "string|in:{$valid_locales}"
        ]);
        if(!$validator->passes()) $locale = 'en';

        app()->setLocale($locale);

        return $next($request);
    }
}
