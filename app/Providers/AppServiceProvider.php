<?php

namespace App\Providers;

use App\Models\Payment;
use App\Observers\PaymentObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Repositories and Services are auto-resolved via constructor injection.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Payment::observe(PaymentObserver::class);
        // Use Bootstrap 5 for pagination (matches layout; fixes pagination styling/errors)
        Paginator::useBootstrapFive();

        View::composer('layouts.app', function ($view) {
            $headerNotifications = [];
            $headerUnreadCount = 0;
            $user = auth()->user();
            if ($user && ($user->can('guest.manage') || $user->can('housekeeping.view') || $user->can('maintenance.manage'))) {
                try {
                    $headerUnreadCount = $user->unreadNotifications()->count();
                    $headerNotifications = $user->notifications()->limit(25)->get();
                } catch (\Throwable $e) {
                    //
                }
            }
            $view->with(compact('headerNotifications', 'headerUnreadCount'));
        });
    }
}
