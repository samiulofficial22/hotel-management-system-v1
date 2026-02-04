<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');
        if (!$locale && auth()->check() && auth()->user()->language_preference) {
            $locale = auth()->user()->language_preference;
        }
        $locales = config('app.available_locales', ['en' => 'English', 'bn' => 'বাংলা']);
        if ($locale && array_key_exists($locale, $locales)) {
            App::setLocale($locale);
        }
        return $next($request);
    }
}
