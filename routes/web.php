<?php

use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\WalletController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\MemberController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\TokenController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// PWA: Web App Manifest (same-origin, no auth)
Route::get('/manifest.webmanifest', function () {
    return response()->json([
        'name' => config('app.name', 'Ledgerly'),
        'short_name' => 'Ledgerly',
        'description' => 'Smart personal finance management. Track income, expenses, and collaborate with your team.',
        'start_url' => url('/'),
        'scope' => url('/'),
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#405189',
        'orientation' => 'portrait-primary',
        'icons' => [
            [
                'src' => asset('assets/minia/images/favicon.ico'),
                'sizes' => '48x48',
                'type' => 'image/x-icon',
                'purpose' => 'any',
            ],
            [
                'src' => asset('assets/minia/images/logo-sm.svg'),
                'sizes' => 'any',
                'type' => 'image/svg+xml',
                'purpose' => 'any maskable',
            ],
        ],
    ], 200, [
        'Content-Type' => 'application/manifest+json',
    ]);
})->name('manifest');

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'account'])->group(function () {
    Route::post('/accounts/switch', [\App\Http\Controllers\Web\AccountController::class, 'switch'])->name('accounts.switch');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('transactions', TransactionController::class);
    Route::resource('wallets', WalletController::class);
    Route::resource('categories', CategoryController::class);
    
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/monthly', [ReportController::class, 'exportMonthly'])->name('reports.export.monthly');
    Route::get('/reports/export/statement', [ReportController::class, 'exportStatement'])->name('reports.export.statement');
    
    // API Tokens management (optional feature)
    // Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    // Route::post('/tokens', [TokenController::class, 'store'])->name('tokens.store');
    // Route::delete('/tokens/{token}', [TokenController::class, 'destroy'])->name('tokens.destroy');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
