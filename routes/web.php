<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CenterController as AdminCenterController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TariffController as AdminTariffController;
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

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin.active'])
    ->group(function (): void {
        Route::get('/', DashboardController::class)
            ->middleware('admin.ability:dashboard.view')
            ->name('home');

        Route::middleware('admin.ability:centers.view')->group(function (): void {
            Route::get('/centers', [AdminCenterController::class, 'index'])->name('centers.index');
        });

        Route::middleware('admin.ability:centers.create')->group(function (): void {
            Route::get('/centers/create', [AdminCenterController::class, 'create'])->name('centers.create');
            Route::post('/centers', [AdminCenterController::class, 'store'])->name('centers.store');
        });

        Route::get('/centers/{center}', [AdminCenterController::class, 'show'])
            ->middleware('admin.ability:centers.view')
            ->name('centers.show');

        Route::middleware('admin.ability:centers.update')->group(function (): void {
            Route::get('/centers/{center}/edit', [AdminCenterController::class, 'edit'])->name('centers.edit');
            Route::put('/centers/{center}', [AdminCenterController::class, 'update'])->name('centers.update');
        });

        Route::delete('/centers/{center}', [AdminCenterController::class, 'destroy'])
            ->middleware('admin.ability:centers.delete')
            ->name('centers.destroy');

        Route::middleware('admin.ability:services.view')->group(function (): void {
            Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        });

        Route::middleware('admin.ability:services.create')->group(function (): void {
            Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
            Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
        });

        Route::get('/services/{service}', [AdminServiceController::class, 'show'])
            ->middleware('admin.ability:services.view')
            ->name('services.show');

        Route::middleware('admin.ability:services.update')->group(function (): void {
            Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
            Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
        });

        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])
            ->middleware('admin.ability:services.delete')
            ->name('services.destroy');

        Route::middleware('admin.ability:tariffs.view')->group(function (): void {
            Route::get('/tariffs', [AdminTariffController::class, 'index'])->name('tariffs.index');
        });

        Route::middleware('admin.ability:tariffs.create')->group(function (): void {
            Route::get('/tariffs/create', [AdminTariffController::class, 'create'])->name('tariffs.create');
            Route::post('/tariffs', [AdminTariffController::class, 'store'])->name('tariffs.store');
        });

        Route::get('/tariffs/{tariff}', [AdminTariffController::class, 'show'])
            ->middleware('admin.ability:tariffs.view')
            ->name('tariffs.show');

        Route::middleware('admin.ability:tariffs.update')->group(function (): void {
            Route::get('/tariffs/{tariff}/edit', [AdminTariffController::class, 'edit'])->name('tariffs.edit');
            Route::put('/tariffs/{tariff}', [AdminTariffController::class, 'update'])->name('tariffs.update');
            Route::post('/tariffs/{tariff}/revisions', [AdminTariffController::class, 'storeRevision'])->name('tariffs.revisions.store');
        });

        Route::delete('/tariffs/{tariff}', [AdminTariffController::class, 'destroy'])
            ->middleware('admin.ability:tariffs.delete')
            ->name('tariffs.destroy');

        Route::middleware('admin.ability:bookings.view')->group(function (): void {
            Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        });

        Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])
            ->middleware('admin.ability:bookings.update')
            ->name('bookings.update');

        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])
            ->middleware('admin.ability:bookings.status.update')
            ->name('bookings.status.update');

        Route::middleware('admin.ability:contact-messages.view')->group(function (): void {
            Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
        });

        Route::put('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'update'])
            ->middleware('admin.ability:contact-messages.update')
            ->name('contact-messages.update');

        Route::patch('/contact-messages/{contactMessage}/status', [AdminContactMessageController::class, 'updateStatus'])
            ->middleware('admin.ability:contact-messages.update')
            ->name('contact-messages.status.update');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
