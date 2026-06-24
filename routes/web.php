<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSite\AboutController;
use App\Http\Controllers\PublicSite\BookingController;
use App\Http\Controllers\PublicSite\CareerController;
use App\Http\Controllers\PublicSite\CenterController;
use App\Http\Controllers\PublicSite\ContactController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\InspectionProcessController;
use App\Http\Controllers\PublicSite\PlaceholderController;
use App\Http\Controllers\PublicSite\ServiceController;
use App\Http\Controllers\PublicSite\StaticPageController;
use App\Http\Controllers\PublicSite\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', ['fr', 'en'])
    ->name('language.switch');

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/centers', [CenterController::class, 'index'])->name('centers.index');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/book-inspection', [BookingController::class, 'create'])->name('book-inspection');
Route::post('/book-inspection', [BookingController::class, 'store'])->name('book-inspection.store');
Route::get('/tariffs', [TariffController::class, 'index'])->name('tariffs');
Route::get('/inspection-process', InspectionProcessController::class)->name('inspection-process');
Route::get('/blog', [PlaceholderController::class, 'blog'])->name('blog.index');
Route::get('/compliance-quality', [PlaceholderController::class, 'compliance'])->name('compliance');
Route::get('/careers', [CareerController::class, 'index'])->name('careers.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/privacy-policy', [StaticPageController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms-and-conditions', [StaticPageController::class, 'terms'])->name('legal.terms');
Route::get('/cookie-policy', [StaticPageController::class, 'cookies'])->name('legal.cookies');
Route::get('/legal-notice', [StaticPageController::class, 'notice'])->name('legal.notice');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
