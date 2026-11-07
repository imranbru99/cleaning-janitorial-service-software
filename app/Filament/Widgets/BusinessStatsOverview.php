<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Inspection;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Support\Tenancy;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user?->isPlatformAdmin() && ! Tenancy::check()) {
            return [
                Stat::make('Businesses', Tenant::query()->count()),
                Stat::make('Active Tenants', Tenant::query()->where('status', 'active')->count()),
                Stat::make('Platform Customers', Customer::withoutGlobalScopes()->count()),
            ];
        }

        return [
            Stat::make('Customers', Customer::query()->count()),
            Stat::make("Today's Bookings", Booking::query()->whereDate('scheduled_start', today())->count()),
            Stat::make('Outstanding Invoices', Invoice::query()->whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->count()),
            Stat::make('Avg Inspection Score', round((float) Inspection::query()->whereNotNull('score')->avg('score'), 1).'%'),
        ];
    }
}
