<?php

namespace App\Filament\Resources;

use App\Models\CleaningJob;
use App\Filament\Resources\CleaningJobResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CleaningJobResource extends Resource
{
    protected static ?string $model = App\Models\CleaningJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return app(\App\Services\Modules\ModuleManager::class)->isActive('jobs')
            || auth()->user()?->isPlatformAdmin();
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference'),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Select::make('customer_id')
                    ->relationship(titleAttribute: 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(['active'=>'Active','inactive'=>'Inactive','pending'=>'Pending','confirmed'=>'Confirmed','assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled','draft'=>'Draft','unpaid'=>'Unpaid','paid'=>'Paid','open'=>'Open','failed'=>'Failed','excellent'=>'Excellent','good'=>'Good','needs_improvement'=>'Needs Improvement','full_time'=>'Full Time','part_time'=>'Part Time','present'=>'Present','one_time'=>'One Time','recurring'=>'Recurring','scheduled'=>'Scheduled']),
                Forms\Components\DateTimePicker::make('scheduled_start'),
                Forms\Components\DateTimePicker::make('scheduled_end'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('scheduled_start')->searchable()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCleaningJobs::route('/'),
            'create' => Pages\CreateCleaningJob::route('/create'),
            'edit' => Pages\EditCleaningJob::route('/{record}/edit'),
        ];
    }
}
