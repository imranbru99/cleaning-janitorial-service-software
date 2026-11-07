<?php

namespace App\Providers;

use App\Models\CleaningJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('job', fn (string $value) => CleaningJob::query()->findOrFail($value));
    }
}
