<?php
use App\Livewire\MarketDetail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Livewire\MarketDashboard;
use App\Livewire\UserBets;
use App\Livewire\AdminDashboard;
use App\Livewire\Leaderboard;

Route::get('/', MarketDashboard::class)->name('dashboard');

Route::get('/home', function() {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ro'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', \App\Livewire\UserProfile::class)->name('profile');
    Route::get('/my-bets', UserBets::class)->name('user.bets');
    Route::get('/leaderboard', Leaderboard::class)->name('leaderboard');

    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
});

require __DIR__.'/auth.php';