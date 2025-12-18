<?php

use App\Livewire\AdminDashboard;
use App\Livewire\DisplaySignage;
use App\Livewire\DisplaySettings;
use App\Livewire\MediaManager;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Main signage player URL
Route::get('/display', [SignageController::class, 'player'])->name('display');
Route::get('/api/playlist.json', [SignageController::class, 'playlist'])->name('playlist');

// Legacy route - keep /signage for backward compatibility
Route::get('/signage', [SignageController::class, 'legacyRedirect'])->name('signage');

// Old Livewire display (kept for preview/testing in admin)
Route::get('/display/preview', DisplaySignage::class)->name('display.preview');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/admin/media', MediaManager::class)->name('admin.media');
    Route::get('/admin/settings', DisplaySettings::class)->name('admin.settings');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
