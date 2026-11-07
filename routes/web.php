<?php

use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\Web\MarketingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/about', [MarketingController::class, 'about'])->name('about');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/modules', [MarketingController::class, 'modules'])->name('modules');
Route::get('/modules/{key}', [MarketingController::class, 'moduleShow'])->name('modules.show');
Route::get('/solutions', [MarketingController::class, 'solutions'])->name('solutions');
Route::get('/solutions/{slug}', [MarketingController::class, 'solutionShow'])->name('solutions.show');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/demo', [MarketingController::class, 'demo'])->name('demo');
Route::post('/demo', [MarketingController::class, 'demoSubmit'])->name('demo.submit');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
Route::post('/contact', [MarketingController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [MarketingController::class, 'faq'])->name('faq');
Route::get('/blog', [MarketingController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [MarketingController::class, 'blogShow'])->name('blog.show');
Route::get('/case-studies', [MarketingController::class, 'caseStudies'])->name('case-studies');
Route::get('/privacy', [MarketingController::class, 'privacy'])->name('privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('terms');
Route::get('/cookies', [MarketingController::class, 'cookies'])->name('cookies');
Route::get('/security', [MarketingController::class, 'security'])->name('security');
Route::get('/status', [MarketingController::class, 'status'])->name('status');
Route::get('/docs', [MarketingController::class, 'docs'])->name('docs');
Route::get('/docs/api', [MarketingController::class, 'apiDocs'])->name('docs.api');
Route::get('/free-trial', [MarketingController::class, 'trial'])->name('trial');
Route::post('/register-tenant', [MarketingController::class, 'registerTenant'])->name('register.tenant');

Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [PortalController::class, 'bookings'])->name('bookings');
    Route::get('/invoices', [PortalController::class, 'invoices'])->name('invoices');
    Route::get('/inspections', [PortalController::class, 'inspections'])->name('inspections');
});
