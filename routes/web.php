<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Platform\PlatformController;
use App\Http\Controllers\PublicSite\BlogController;
use App\Http\Controllers\PublicSite\EventController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\MemberController;
use App\Http\Controllers\Webhook\PayPalWebhookController;
use Illuminate\Support\Facades\Route;

// ─── PayPal Webhook (no CSRF) ───
Route::post('webhook/paypal/{tenantSlug}', [PayPalWebhookController::class, 'handle'])
    ->name('webhook.paypal');

// ─── Auth Routes ───
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// ─── Platform Admin (main domain only) ───
Route::prefix('platform')->middleware(['auth', 'platform.admin'])->group(function () {
    Route::get('/', [PlatformController::class, 'dashboard'])->name('platform.dashboard');
    Route::get('/tenants/create', [PlatformController::class, 'createTenant'])->name('platform.tenants.create');
    Route::post('/tenants', [PlatformController::class, 'storeTenant'])->name('platform.tenants.store');
    Route::get('/tenants/{tenant}/edit', [PlatformController::class, 'editTenant'])->name('platform.tenants.edit');
    Route::put('/tenants/{tenant}', [PlatformController::class, 'updateTenant'])->name('platform.tenants.update');
    Route::get('/tenants/{tenant}/purge', [PlatformController::class, 'confirmPurge'])->name('platform.tenants.purge');
    Route::delete('/tenants/{tenant}/purge', [PlatformController::class, 'executePurge'])->name('platform.tenants.purge.execute');
});

// ─── Tenant Admin ───
Route::prefix('admin')->middleware(['auth', 'tenant.exists', 'governing'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Home Content
    Route::get('/home-content', [HomeContentController::class, 'index'])->name('home-content.index');
    Route::get('/home-content/carousel/create', [HomeContentController::class, 'createCarousel'])->name('home-content.carousel.create');
    Route::post('/home-content/carousel', [HomeContentController::class, 'storeCarousel'])->name('home-content.carousel.store');
    Route::get('/home-content/carousel/{carouselItem}/edit', [HomeContentController::class, 'editCarousel'])->name('home-content.carousel.edit');
    Route::put('/home-content/carousel/{carouselItem}', [HomeContentController::class, 'updateCarousel'])->name('home-content.carousel.update');
    Route::delete('/home-content/carousel/{carouselItem}', [HomeContentController::class, 'destroyCarousel'])->name('home-content.carousel.destroy');

    Route::get('/home-content/blocks/create', [HomeContentController::class, 'createBlock'])->name('home-content.blocks.create');
    Route::post('/home-content/blocks', [HomeContentController::class, 'storeBlock'])->name('home-content.blocks.store');
    Route::get('/home-content/blocks/{contentBlock}/edit', [HomeContentController::class, 'editBlock'])->name('home-content.blocks.edit');
    Route::put('/home-content/blocks/{contentBlock}', [HomeContentController::class, 'updateBlock'])->name('home-content.blocks.update');
    Route::delete('/home-content/blocks/{contentBlock}', [HomeContentController::class, 'destroyBlock'])->name('home-content.blocks.destroy');

    // Blog
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');

    // Events
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

    // Ticket Types
    Route::post('/events/{event}/ticket-types', [AdminEventController::class, 'storeTicketType'])->name('events.ticket-types.store');
    Route::put('/events/{event}/ticket-types/{ticketType}', [AdminEventController::class, 'updateTicketType'])->name('events.ticket-types.update');
    Route::delete('/events/{event}/ticket-types/{ticketType}', [AdminEventController::class, 'destroyTicketType'])->name('events.ticket-types.destroy');

    // Event Images
    Route::post('/events/{event}/images', [AdminEventController::class, 'storeImage'])->name('events.images.store');
    Route::delete('/events/{event}/images/{image}', [AdminEventController::class, 'destroyImage'])->name('events.images.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/refund', [OrderController::class, 'markRefunded'])->name('orders.refund');

    // RSVPs
    Route::get('/rsvps', [OrderController::class, 'rsvps'])->name('orders.rsvps');

    // Cash Collections
    Route::get('/cash', [OrderController::class, 'cashIndex'])->name('orders.cash');
    Route::post('/cash', [OrderController::class, 'storeCash'])->name('orders.cash.store');

    // POS Payments
    Route::get('/pos', [OrderController::class, 'posIndex'])->name('orders.pos');
    Route::post('/pos', [OrderController::class, 'storePos'])->name('orders.pos.store');

    // Members
    Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [AdminMemberController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/edit', [AdminMemberController::class, 'edit'])->name('members.edit');
    Route::put('/members/{member}', [AdminMemberController::class, 'update'])->name('members.update');
    Route::post('/members/{member}/approve', [AdminMemberController::class, 'approve'])->name('members.approve');
    Route::post('/members/{member}/promote', [AdminMemberController::class, 'promote'])->name('members.promote');
    Route::post('/members/{member}/suspend', [AdminMemberController::class, 'suspend'])->name('members.suspend');
    Route::post('/members/{member}/activate', [AdminMemberController::class, 'activate'])->name('members.activate');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/tenant', [SettingsController::class, 'updateTenant'])->name('settings.tenant.update');
    Route::put('/settings/paypal', [SettingsController::class, 'updatePaypal'])->name('settings.paypal.update');
});

// ─── Public Tenant Routes ───
Route::middleware(['tenant.exists'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Blog
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

    // Events
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
    Route::post('/events/{slug}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');
    Route::post('/events/{slug}/checkout', [EventController::class, 'checkout'])->name('events.checkout');
    Route::get('/events/{slug}/checkout/success/{order}', [EventController::class, 'checkoutSuccess'])->name('events.checkout.success');
    Route::get('/events/{slug}/checkout/cancel/{order}', [EventController::class, 'checkoutCancel'])->name('events.checkout.cancel');

    // Member Signup
    Route::get('/join', [MemberController::class, 'showSignupForm'])->name('members.signup');
    Route::post('/join', [MemberController::class, 'signup'])->name('members.signup.store');
});

// Note: File serving handled by Laravel's built-in storage.local route (filesystem 'serve' => true)
