<?php

use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CareerDepartmentController as AdminCareerDepartmentController;
use App\Http\Controllers\Admin\CareerPostController as AdminCareerPostController;
use App\Http\Controllers\Admin\CenterController as AdminCenterController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TariffController as AdminTariffController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

        Route::middleware('admin.ability:blog.view')->group(function (): void {
            Route::get('/blog-posts', [AdminBlogPostController::class, 'index'])->name('blog-posts.index');
            Route::get('/blog-categories', [AdminBlogCategoryController::class, 'index'])->name('blog-categories.index');
        });

        Route::middleware('admin.ability:blog.create')->group(function (): void {
            Route::get('/blog-posts/create', [AdminBlogPostController::class, 'create'])->name('blog-posts.create');
            Route::post('/blog-posts', [AdminBlogPostController::class, 'store'])->name('blog-posts.store');
            Route::get('/blog-categories/create', [AdminBlogCategoryController::class, 'create'])->name('blog-categories.create');
            Route::post('/blog-categories', [AdminBlogCategoryController::class, 'store'])->name('blog-categories.store');
        });

        Route::get('/blog-posts/{blogPost}', [AdminBlogPostController::class, 'show'])
            ->middleware('admin.ability:blog.view')
            ->name('blog-posts.show');

        Route::get('/blog-categories/{blogCategory}', [AdminBlogCategoryController::class, 'show'])
            ->middleware('admin.ability:blog.view')
            ->name('blog-categories.show');

        Route::middleware('admin.ability:blog.update')->group(function (): void {
            Route::get('/blog-posts/{blogPost}/edit', [AdminBlogPostController::class, 'edit'])->name('blog-posts.edit');
            Route::put('/blog-posts/{blogPost}', [AdminBlogPostController::class, 'update'])->name('blog-posts.update');
            Route::get('/blog-categories/{blogCategory}/edit', [AdminBlogCategoryController::class, 'edit'])->name('blog-categories.edit');
            Route::put('/blog-categories/{blogCategory}', [AdminBlogCategoryController::class, 'update'])->name('blog-categories.update');
        });

        Route::delete('/blog-posts/{blogPost}', [AdminBlogPostController::class, 'destroy'])
            ->middleware('admin.ability:blog.delete')
            ->name('blog-posts.destroy');

        Route::delete('/blog-categories/{blogCategory}', [AdminBlogCategoryController::class, 'destroy'])
            ->middleware('admin.ability:blog.delete')
            ->name('blog-categories.destroy');

        Route::middleware('admin.ability:careers.view')->group(function (): void {
            Route::get('/career-posts', [AdminCareerPostController::class, 'index'])->name('career-posts.index');
            Route::get('/career-departments', [AdminCareerDepartmentController::class, 'index'])->name('career-departments.index');
        });

        Route::middleware('admin.ability:careers.create')->group(function (): void {
            Route::get('/career-posts/create', [AdminCareerPostController::class, 'create'])->name('career-posts.create');
            Route::post('/career-posts', [AdminCareerPostController::class, 'store'])->name('career-posts.store');
            Route::get('/career-departments/create', [AdminCareerDepartmentController::class, 'create'])->name('career-departments.create');
            Route::post('/career-departments', [AdminCareerDepartmentController::class, 'store'])->name('career-departments.store');
        });

        Route::get('/career-posts/{careerPost}', [AdminCareerPostController::class, 'show'])
            ->middleware('admin.ability:careers.view')
            ->name('career-posts.show');

        Route::get('/career-departments/{careerDepartment}', [AdminCareerDepartmentController::class, 'show'])
            ->middleware('admin.ability:careers.view')
            ->name('career-departments.show');

        Route::middleware('admin.ability:careers.update')->group(function (): void {
            Route::get('/career-posts/{careerPost}/edit', [AdminCareerPostController::class, 'edit'])->name('career-posts.edit');
            Route::put('/career-posts/{careerPost}', [AdminCareerPostController::class, 'update'])->name('career-posts.update');
            Route::get('/career-departments/{careerDepartment}/edit', [AdminCareerDepartmentController::class, 'edit'])->name('career-departments.edit');
            Route::put('/career-departments/{careerDepartment}', [AdminCareerDepartmentController::class, 'update'])->name('career-departments.update');
        });

        Route::delete('/career-posts/{careerPost}', [AdminCareerPostController::class, 'destroy'])
            ->middleware('admin.ability:careers.delete')
            ->name('career-posts.destroy');

        Route::delete('/career-departments/{careerDepartment}', [AdminCareerDepartmentController::class, 'destroy'])
            ->middleware('admin.ability:careers.delete')
            ->name('career-departments.destroy');

        Route::get('/pages', [AdminPageController::class, 'index'])
            ->middleware('admin.ability:pages.view')
            ->name('pages.index');

        Route::middleware('admin.ability:pages.create')->group(function (): void {
            Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
            Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
        });

        Route::get('/pages/{page}', [AdminPageController::class, 'show'])
            ->middleware('admin.ability:pages.view')
            ->name('pages.show');

        Route::middleware('admin.ability:pages.update')->group(function (): void {
            Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
            Route::put('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
        });

        Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])
            ->middleware('admin.ability:pages.delete')
            ->name('pages.destroy');

        Route::get('/media', [AdminMediaController::class, 'index'])
            ->middleware('admin.ability:media.view')
            ->name('media.index');

        Route::middleware('admin.ability:media.create')->group(function (): void {
            Route::get('/media/create', [AdminMediaController::class, 'create'])->name('media.create');
            Route::post('/media', [AdminMediaController::class, 'store'])->name('media.store');
        });

        Route::get('/media/{medium}', [AdminMediaController::class, 'show'])
            ->middleware('admin.ability:media.view')
            ->name('media.show');

        Route::middleware('admin.ability:media.update')->group(function (): void {
            Route::get('/media/{medium}/edit', [AdminMediaController::class, 'edit'])->name('media.edit');
            Route::put('/media/{medium}', [AdminMediaController::class, 'update'])->name('media.update');
        });

        Route::delete('/media/{medium}', [AdminMediaController::class, 'destroy'])
            ->middleware('admin.ability:media.delete')
            ->name('media.destroy');

        Route::middleware('admin.ability:users.create')->group(function (): void {
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        });

        Route::middleware('admin.ability:users.view')->group(function (): void {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        });

        Route::middleware('admin.ability:users.update')->group(function (): void {
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        });

        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
            ->middleware('admin.ability:users.delete')
            ->name('users.destroy');

        Route::middleware('admin.ability:roles.view')->group(function (): void {
            Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/{role}', [AdminRoleController::class, 'show'])->name('roles.show');
        });

        Route::middleware('admin.ability:roles.update')->group(function (): void {
            Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
        });
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
