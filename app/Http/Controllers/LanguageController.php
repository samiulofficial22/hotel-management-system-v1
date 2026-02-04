<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate(['locale' => 'required|string|in:en,bn']);
        $locale = $request->locale;
        session(['locale' => $locale]);
        App::setLocale($locale);
        if (auth()->check()) {
            auth()->user()->update(['language_preference' => $locale]);
        }
        return redirect()->back();
    }
}
