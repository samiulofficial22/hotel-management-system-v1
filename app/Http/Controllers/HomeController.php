<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public home page (no login required).
     * Authenticated users are redirected to the dashboard.
     */
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $roomTypes = RoomType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('public.home', compact('roomTypes'));
    }
}
