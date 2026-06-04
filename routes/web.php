<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', [LanguageController::class, 'switch'])
    ->whereIn('locale', ['fr', 'en'])
    ->name('language.switch');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('/design-system', 'public.design-system')->name('design-system');

Route::view('/about', 'public.placeholder', ['pageTitle' => 'navigation.about'])->name('about');
Route::view('/centers', 'public.placeholder', ['pageTitle' => 'navigation.centers'])->name('centers.index');
Route::view('/services', 'public.placeholder', ['pageTitle' => 'navigation.services'])->name('services.index');
Route::view('/book-inspection', 'public.placeholder', ['pageTitle' => 'navigation.book'])->name('book-inspection');
Route::view('/tariffs', 'public.placeholder', ['pageTitle' => 'navigation.tariffs'])->name('tariffs');
Route::view('/inspection-process', 'public.placeholder', ['pageTitle' => 'navigation.inspection_process'])->name('inspection-process');
Route::view('/blog', 'public.placeholder', ['pageTitle' => 'navigation.blog'])->name('blog.index');
Route::view('/compliance-quality', 'public.placeholder', ['pageTitle' => 'navigation.compliance'])->name('compliance');
Route::view('/careers', 'public.placeholder', ['pageTitle' => 'navigation.careers'])->name('careers.index');
Route::view('/contact', 'public.placeholder', ['pageTitle' => 'navigation.contact'])->name('contact');

Route::view('/privacy-policy', 'public.placeholder', ['pageTitle' => 'footer.privacy'])->name('legal.privacy');
Route::view('/terms-and-conditions', 'public.placeholder', ['pageTitle' => 'footer.terms'])->name('legal.terms');
Route::view('/cookie-policy', 'public.placeholder', ['pageTitle' => 'footer.cookies'])->name('legal.cookies');
Route::view('/legal-notice', 'public.placeholder', ['pageTitle' => 'footer.legal_notice'])->name('legal.notice');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
