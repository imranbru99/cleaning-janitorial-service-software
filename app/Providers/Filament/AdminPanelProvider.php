<?php

namespace App\Providers\Filament;

use App\Http\Middleware\InitializeTenancy;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(config('cleaning.short_name', 'CleanOps'))
            ->colors([
                'primary' => Color::hex('#0B1F3A'),
                'info' => Color::hex('#0D9488'),
                'success' => Color::hex('#059669'),
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('CRM'),
                NavigationGroup::make('Catalog'),
                NavigationGroup::make('Operations'),
                NavigationGroup::make('Quality'),
                NavigationGroup::make('Workforce'),
                NavigationGroup::make('Sales'),
                NavigationGroup::make('Finance'),
                NavigationGroup::make('Inventory'),
                NavigationGroup::make('Support'),
                NavigationGroup::make('Organization'),
                NavigationGroup::make('Analytics'),
                NavigationGroup::make('AI'),
                NavigationGroup::make('System'),
                NavigationGroup::make('Platform'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\BusinessStatsOverview::class,
                \App\Filament\Widgets\TodaysOperationsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                InitializeTenancy::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
