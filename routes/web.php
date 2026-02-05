<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\BanquetVenueController;
use App\Http\Controllers\BanquetBookingController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\MinibarController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\MarketingCampaignController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GuestRequestController;
use App\Http\Controllers\GuestApprovalController;
use App\Http\Controllers\GuestPortalController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Hotel Management Admin
|--------------------------------------------------------------------------
*/

// Language switcher (auth optional)
Route::post('/language', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch')->middleware('web');

// Public guest booking request (no login required)
Route::get('/booking/request', [GuestRequestController::class, 'create'])->name('booking.request');
Route::post('/booking/request', [GuestRequestController::class, 'store'])->name('booking.request.store');

// Auth routes (guest only) - must be defined first so login page is findable
Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Profile (any authenticated user)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Guest panel (guest role only – checked in controller to avoid custom middleware dependency)
    Route::prefix('guest')->name('guest.')->group(function (): void {
        Route::get('/', [\App\Http\Controllers\GuestPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\GuestPortalController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\GuestPortalController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password', [\App\Http\Controllers\GuestPortalController::class, 'updatePassword'])->name('password.update');
        Route::get('/bookings', [\App\Http\Controllers\GuestPortalController::class, 'bookings'])->name('bookings');
    });

    // Guest booking requests (admin / guest.manage)
    Route::middleware('permission:guest.manage')->group(function (): void {
        Route::get('/guest-requests', [GuestApprovalController::class, 'index'])->name('guest.requests.index');
        Route::post('/guest-requests/{booking}/approve', [GuestApprovalController::class, 'approve'])->name('guest.requests.approve');
        Route::post('/guest-requests/{booking}/reject', [GuestApprovalController::class, 'reject'])->name('guest.requests.reject');
    });

    // Users (admin / users.manage)
    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('permission:users.manage');

    // Room types & rooms
    Route::resource('room-types', RoomTypeController::class)->except(['show'])->middleware('permission:room_types.manage');
    Route::resource('rooms', RoomController::class)->except(['show'])->middleware('permission:rooms.manage');

    // Guests
    Route::resource('guests', GuestController::class)->middleware('permission:guests.manage');
    Route::post('guests/{guest}/revoke-portal', [GuestController::class, 'revokePortal'])
        ->name('guests.revoke-portal')
        ->middleware('permission:guests.manage');

    // Bookings
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar')->middleware('permission:bookings.manage');
    Route::resource('bookings', BookingController::class)->middleware('permission:bookings.manage');
    Route::post('/bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('bookings.check-in')->middleware('permission:bookings.checkin_checkout');
    Route::post('/bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out')->middleware('permission:bookings.checkin_checkout');

    // Invoice PDF download (Phase 6)
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf')->middleware('permission:invoices.manage');

    // Phase 2: F&B / POS
    Route::middleware('permission:pos.manage')->group(function (): void {
        Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
        Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
        Route::post('/outlets', [OutletController::class, 'store'])->name('outlets.store');
        Route::get('/outlets/{outlet}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
        Route::put('/outlets/{outlet}', [OutletController::class, 'update'])->name('outlets.update');

        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/orders', [PosController::class, 'ordersList'])->name('pos.orders-list');
        Route::get('/pos/outlet/{id}', [PosController::class, 'outlet'])->name('pos.outlet');
        Route::post('/pos/order', [PosController::class, 'createOrder'])->name('pos.order.create');
        Route::get('/pos/order/{order}', [PosController::class, 'order'])->name('pos.order');
        Route::post('/pos/order/{order}/item', [PosController::class, 'addItem'])->name('pos.order.add-item');
        Route::delete('/pos/order/{order}/item/{item}', [PosController::class, 'removeItem'])->name('pos.order.remove-item');
        Route::post('/pos/order/{order}/send-kitchen', [PosController::class, 'sendToKitchen'])->name('pos.order.send-kitchen');
        Route::post('/pos/order/{order}/complete', [PosController::class, 'completeOrder'])->name('pos.order.complete');

        Route::prefix('outlets/{outlet}')->name('menu.')->group(function (): void {
            Route::get('menu/categories', [MenuCategoryController::class, 'index'])->name('categories.index');
            Route::get('menu/categories/create', [MenuCategoryController::class, 'create'])->name('categories.create');
            Route::post('menu/categories', [MenuCategoryController::class, 'store'])->name('categories.store');
            Route::get('menu/categories/{category}/edit', [MenuCategoryController::class, 'edit'])->name('categories.edit');
            Route::put('menu/categories/{category}', [MenuCategoryController::class, 'update'])->name('categories.update');
            Route::get('menu/items', [MenuItemController::class, 'index'])->name('items.index');
            Route::get('menu/items/create', [MenuItemController::class, 'create'])->name('items.create');
            Route::post('menu/items', [MenuItemController::class, 'store'])->name('items.store');
            Route::get('menu/items/{item}/edit', [MenuItemController::class, 'edit'])->name('items.edit');
            Route::put('menu/items/{item}', [MenuItemController::class, 'update'])->name('items.update');
        });
    });

    Route::middleware('permission:kitchen.view')->group(function (): void {
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::post('/kitchen/item/{pos_order_item}/ready', [KitchenController::class, 'markItemReady'])->name('kitchen.item.ready');
    });

    // Phase 2: Banquet
    Route::middleware('permission:banquet.manage')->group(function (): void {
        Route::prefix('banquet')->name('banquet.')->group(function (): void {
            Route::get('venues', [BanquetVenueController::class, 'index'])->name('venues.index');
            Route::get('venues/create', [BanquetVenueController::class, 'create'])->name('venues.create');
            Route::post('venues', [BanquetVenueController::class, 'store'])->name('venues.store');
            Route::get('venues/{venue}/edit', [BanquetVenueController::class, 'edit'])->name('venues.edit');
            Route::put('venues/{venue}', [BanquetVenueController::class, 'update'])->name('venues.update');
            Route::resource('bookings', BanquetBookingController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        });
    });

    // Phase 3: Housekeeping, Minibar, Store, Maintenance
    Route::middleware('permission:housekeeping.view')->group(function (): void {
        Route::get('/housekeeping', [HousekeepingController::class, 'index'])->name('housekeeping.index');
        Route::post('/housekeeping/assign', [HousekeepingController::class, 'assign'])->name('housekeeping.assign')->middleware('permission:housekeeping.manage');
        Route::post('/housekeeping/{assignment}/reassign', [HousekeepingController::class, 'reassign'])->name('housekeeping.reassign')->middleware('permission:housekeeping.manage');
        Route::post('/housekeeping/{assignment}/start', [HousekeepingController::class, 'start'])->name('housekeeping.start')->middleware('permission:housekeeping.manage');
        Route::post('/housekeeping/{assignment}/complete', [HousekeepingController::class, 'complete'])->name('housekeeping.complete')->middleware('permission:housekeeping.manage');
    });
    Route::middleware('permission:minibar.manage')->group(function (): void {
        Route::get('/minibar', [MinibarController::class, 'index'])->name('minibar.index');
        Route::get('/minibar/create', [MinibarController::class, 'create'])->name('minibar.create');
        Route::post('/minibar', [MinibarController::class, 'store'])->name('minibar.store');
        Route::get('/minibar/{minibar_item}/edit', [MinibarController::class, 'edit'])->name('minibar.edit');
        Route::put('/minibar/{minibar_item}', [MinibarController::class, 'update'])->name('minibar.update');
    });
    Route::middleware('permission:store.manage')->group(function (): void {
        Route::get('/store', [StoreController::class, 'index'])->name('store.index');
        Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
        Route::post('/store', [StoreController::class, 'store'])->name('store.store');
        Route::get('/store/{store_item}/edit', [StoreController::class, 'edit'])->name('store.edit');
        Route::put('/store/{store_item}', [StoreController::class, 'update'])->name('store.update');
    });
    Route::middleware('permission:maintenance.view')->group(function (): void {
        Route::get('/maintenance', [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/create', [MaintenanceRequestController::class, 'create'])->name('maintenance.create')->middleware('permission:maintenance.manage');
        Route::post('/maintenance', [MaintenanceRequestController::class, 'store'])->name('maintenance.store')->middleware('permission:maintenance.manage');
        Route::get('/maintenance/{maintenance_request}', [MaintenanceRequestController::class, 'show'])->name('maintenance.show');
        Route::get('/maintenance/{maintenance_request}/edit', [MaintenanceRequestController::class, 'edit'])->name('maintenance.edit')->middleware('permission:maintenance.manage');
        Route::put('/maintenance/{maintenance_request}', [MaintenanceRequestController::class, 'update'])->name('maintenance.update')->middleware('permission:maintenance.manage');
    });

    // Phase 4: Accounts, HR, Marketing, Reports
    Route::middleware('permission:accounts.view')->group(function (): void {
        Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/ledger', [AccountsController::class, 'ledger'])->name('accounts.ledger');
        Route::post('/accounts/entry', [AccountsController::class, 'storeEntry'])->name('accounts.entry.store')->middleware('permission:accounts.manage');
        Route::get('/accounts/create', [AccountsController::class, 'create'])->name('accounts.create')->middleware('permission:accounts.manage');
        Route::post('/accounts', [AccountsController::class, 'store'])->name('accounts.store')->middleware('permission:accounts.manage');
        Route::get('/accounts/{account}/edit', [AccountsController::class, 'edit'])->name('accounts.edit')->middleware('permission:accounts.manage');
        Route::put('/accounts/{account}', [AccountsController::class, 'update'])->name('accounts.update')->middleware('permission:accounts.manage');
    });
    Route::middleware('permission:hr.view')->group(function (): void {
        Route::prefix('hr')->name('hr.')->group(function (): void {
            Route::resource('employees', EmployeeController::class)->middleware('permission:hr.manage');
            Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::post('attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark')->middleware('permission:hr.manage');
            Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index')->middleware('permission:payroll.view');
            Route::get('payroll/create', [PayrollController::class, 'create'])->name('payroll.create')->middleware('permission:payroll.manage');
            Route::post('payroll', [PayrollController::class, 'store'])->name('payroll.store')->middleware('permission:payroll.manage');
            Route::get('payroll/{payroll_run}', [PayrollController::class, 'show'])->name('payroll.show')->middleware('permission:payroll.view');
            Route::post('payroll/{payroll_run}/process', [PayrollController::class, 'process'])->name('payroll.process')->middleware('permission:payroll.manage');
            Route::put('payroll/item/{payroll_item}', [PayrollController::class, 'updateItem'])->name('payroll.item.update')->middleware('permission:payroll.manage');
        });
    });
    Route::middleware('permission:marketing.view')->group(function (): void {
        Route::get('/marketing', [MarketingCampaignController::class, 'index'])->name('marketing.index');
        Route::get('/marketing/create', [MarketingCampaignController::class, 'create'])->name('marketing.create')->middleware('permission:marketing.manage');
        Route::post('/marketing', [MarketingCampaignController::class, 'store'])->name('marketing.store')->middleware('permission:marketing.manage');
        Route::get('/marketing/{marketing_campaign}', [MarketingCampaignController::class, 'show'])->name('marketing.show');
        Route::get('/marketing/{marketing_campaign}/edit', [MarketingCampaignController::class, 'edit'])->name('marketing.edit')->middleware('permission:marketing.manage');
        Route::put('/marketing/{marketing_campaign}', [MarketingCampaignController::class, 'update'])->name('marketing.update')->middleware('permission:marketing.manage');
        Route::post('/marketing/{marketing_campaign}/recipients', [MarketingCampaignController::class, 'addRecipients'])->name('marketing.recipients')->middleware('permission:marketing.manage');
    });
    Route::middleware('permission:reports.view')->group(function (): void {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    });

    // NEW – SAFE ADDITION: Settings → Departments
    Route::middleware('permission:departments.manage')->prefix('settings')->name('settings.')->group(function (): void {
        Route::resource('departments', DepartmentController::class)->except(['show']);
    });
});

// Public home page; authenticated users are redirected to dashboard
Route::get('/', [HomeController::class, 'index'])->name('home');
