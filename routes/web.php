<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyArticleController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VolunteerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/code-of-conduct', [PageController::class, 'conduct'])->name('conduct');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/community', [PageController::class, 'community'])->name('community');
Route::post('/community/volunteer', [VolunteerController::class, 'store'])
    ->middleware('throttle:5,10')->name('volunteer.store');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,10')->name('contact.store');

Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/topic/{category}', [GuideController::class, 'category'])->name('guides.category');
Route::get('/guides/{article}', [GuideController::class, 'show'])->name('guides.show');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{post}', [NewsController::class, 'show'])->name('news.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,10');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:5,10')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')->name('verification.send');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Member area: write and track your own guides
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [MyArticleController::class, 'index'])->name('dashboard');
    Route::post('/preview', [MyArticleController::class, 'preview'])->middleware('throttle:30,1')->name('dashboard.preview');
    Route::get('/articles/create', [MyArticleController::class, 'create'])->name('dashboard.articles.create');
    Route::post('/articles', [MyArticleController::class, 'store'])->name('dashboard.articles.store');
    Route::get('/articles/{article}/edit', [MyArticleController::class, 'edit'])->name('dashboard.articles.edit');
    Route::put('/articles/{article}', [MyArticleController::class, 'update'])->name('dashboard.articles.update');
    Route::delete('/articles/{article}', [MyArticleController::class, 'destroy'])->name('dashboard.articles.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin area (editors + admins)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('/articles', [Admin\ArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles/{article}/publish', [Admin\ArticleController::class, 'publish'])->name('articles.publish');
    Route::post('/articles/{article}/reject', [Admin\ArticleController::class, 'reject'])->name('articles.reject');
    Route::post('/articles/{article}/unpublish', [Admin\ArticleController::class, 'unpublish'])->name('articles.unpublish');
    Route::post('/articles/{article}/feature', [Admin\ArticleController::class, 'feature'])->name('articles.feature');
    Route::delete('/articles/{article}', [Admin\ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::resource('posts', Admin\PostController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->except(['show', 'create']);

    Route::get('/messages', [Admin\InboxController::class, 'messages'])->name('messages.index');
    Route::get('/messages/{message}', [Admin\InboxController::class, 'showMessage'])->name('messages.show');
    Route::delete('/messages/{message}', [Admin\InboxController::class, 'destroyMessage'])->name('messages.destroy');
    Route::get('/volunteers', [Admin\InboxController::class, 'volunteers'])->name('volunteers.index');
    Route::post('/volunteers/{volunteer}/contacted', [Admin\InboxController::class, 'contactVolunteer'])->name('volunteers.contacted');
    Route::delete('/volunteers/{volunteer}', [Admin\InboxController::class, 'destroyVolunteer'])->name('volunteers.destroy');

    Route::middleware('can:manage-users')->group(function () {
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    });
});
