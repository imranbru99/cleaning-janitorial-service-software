<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\CleaningJob;
use App\Models\Inspection;
use App\Models\Invoice;
use App\Services\Modules\ModuleManager;
use Filament\Pages\Page;

class OperationsReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Analytics';

    protected static string $view = 'filament.pages.operations-reports';

    public int $bookings = 0;

    public int $completedJobs = 0;

    public float $avgInspection = 0;

    public float $outstanding = 0;

    public static function shouldRegisterNavigation(): bool
    {
        return app(ModuleManager::class)->isActive('reports')
            || auth()->user()?->isPlatformAdmin();
    }

    public function mount(): void
    {
        $this->bookings = Booking::query()->count();
        $this->completedJobs = CleaningJob::query()->where('status', 'completed')->count();
        $this->avgInspection = round((float) Inspection::query()->whereNotNull('score')->avg('score'), 1);
        $this->outstanding = (float) Invoice::query()->whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->sum('balance_due');
    }
}
