<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\CleaningJob;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodaysOperationsWidget extends BaseWidget
{
    protected static ?string $heading = "Today's Jobs";

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CleaningJob::query()
                    ->whereDate('scheduled_start', today())
                    ->latest('scheduled_start')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')->label('Ref'),
                Tables\Columns\TextColumn::make('title')->limit(40),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('scheduled_start')->dateTime(),
            ])
            ->paginated(false);
    }
}
