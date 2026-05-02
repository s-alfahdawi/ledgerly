<?php

use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\WalletController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\MemberController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\TokenController;
use App\Http\Controllers\Web\BudgetController;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\OnboardingController;
use App\Http\Controllers\Web\RecurringTransactionController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\AttachmentController;
use App\Http\Controllers\Web\TagController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// PWA: Web App Manifest (same-origin, no auth)
Route::get('/manifest.webmanifest', function () {
    $baseUrl = rtrim(config('app.url', url('/')), '/');
    $icons = [
        ['src' => $baseUrl . '/assets/minia/images/favicon.ico', 'sizes' => '48x48', 'type' => 'image/x-icon', 'purpose' => 'any'],
        ['src' => $baseUrl . '/assets/minia/images/logo-sm.svg', 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
    ];
    // Chrome needs 192x192 and 512x512 for "Add to Home Screen" – use PNGs in public/icons/ if present
    if (file_exists(public_path('icons/icon-192.png'))) {
        $icons[] = ['src' => $baseUrl . '/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'];
    } else {
        $icons[] = ['src' => $baseUrl . '/assets/minia/images/favicon.ico', 'sizes' => '192x192', 'type' => 'image/x-icon', 'purpose' => 'any'];
    }
    if (file_exists(public_path('icons/icon-512.png'))) {
        $icons[] = ['src' => $baseUrl . '/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'];
    } else {
        $icons[] = ['src' => $baseUrl . '/assets/minia/images/favicon.ico', 'sizes' => '512x512', 'type' => 'image/x-icon', 'purpose' => 'any maskable'];
    }

    return response()->json([
        'name' => config('app.name', 'Ledgerly'),
        'short_name' => 'Ledgerly',
        'description' => 'Smart personal finance management. Track income, expenses, and collaborate with your team.',
        'start_url' => $baseUrl . '/',
        'scope' => $baseUrl . '/',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#405189',
        'orientation' => 'portrait-primary',
        'icons' => $icons,
    ], 200, [
        'Content-Type' => 'application/manifest+json',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('manifest');

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Onboarding wizard (auth + account context, but NO onboarding check)
Route::middleware(['auth', 'account'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding/account', [OnboardingController::class, 'setupAccount'])->name('onboarding.account');
    Route::post('/onboarding/wallets', [OnboardingController::class, 'setupWallets'])->name('onboarding.wallets');
    Route::post('/onboarding/categories', [OnboardingController::class, 'setupCategories'])->name('onboarding.categories');
    Route::get('/onboarding/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');
});

Route::middleware(['auth', 'account', \App\Http\Middleware\EnsureOnboardingComplete::class])->group(function () {
    Route::post('/accounts/switch', [\App\Http\Controllers\Web\AccountController::class, 'switch'])->name('accounts.switch');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/cash-match', [DashboardController::class, 'cashMatch'])->name('dashboard.cash-match');
    
    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions/{transaction}/duplicate', [TransactionController::class, 'duplicate'])->name('transactions.duplicate');
    Route::post('/transactions/bulk-action', [TransactionController::class, 'bulkAction'])->name('transactions.bulk');
    Route::resource('wallets', WalletController::class);
    Route::resource('categories', CategoryController::class);
    
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/monthly', [ReportController::class, 'exportMonthly'])->name('reports.export.monthly');
    Route::get('/reports/export/statement', [ReportController::class, 'exportStatement'])->name('reports.export.statement');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    
    // API Tokens management (optional feature)
    // Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    // Route::post('/tokens', [TokenController::class, 'store'])->name('tokens.store');
    // Route::delete('/tokens/{token}', [TokenController::class, 'destroy'])->name('tokens.destroy');
    
    Route::resource('recurring', RecurringTransactionController::class)->except(['show']);
    Route::post('/recurring/{recurring}/toggle', [RecurringTransactionController::class, 'toggle'])->name('recurring.toggle');

    Route::resource('budgets', BudgetController::class)->except(['show']);

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/preview', [ImportController::class, 'preview'])->name('import.preview');
    Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
