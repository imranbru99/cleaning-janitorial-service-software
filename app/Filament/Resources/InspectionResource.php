<?php

namespace App\Filament\Resources;

use App\Models\Inspection;
use App\Filament\Resources\InspectionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InspectionResource extends Resource
{
    protected static ?string $model = App\Models\Inspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Quality';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return app(\App\Services\Modules\ModuleManager::class)->isActive('quality')
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
                Forms\Components\Select::make('cleaning_job_id')
                    ->relationship(titleAttribute: 'title'),
                Forms\Components\Select::make('customer_id')
                    ->relationship(titleAttribute: 'name'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(['active'=>'Active','inactive'=>'Inactive','pending'=>'Pending','confirmed'=>'Confirmed','assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled','draft'=>'Draft','unpaid'=>'Unpaid','paid'=>'Paid','open'=>'Open','failed'=>'Failed','excellent'=>'Excellent','good'=>'Good','needs_improvement'=>'Needs Improvement','full_time'=>'Full Time','part_time'=>'Part Time','present'=>'Present','one_time'=>'One Time','recurring'=>'Recurring','scheduled'=>'Scheduled']),
                Forms\Components\Select::make('result')
                    ->options(['pending'=>'Pending','excellent'=>'Excellent','good'=>'Good','needs_improvement'=>'Needs Improvement','failed'=>'Failed']),
                Forms\Components\TextInput::make('score')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('scheduled_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('result')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('score')->sortable(),
                Tables\Columns\TextColumn::make('scheduled_at')->searchable()->sortable(),
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
            'index' => Pages\ListInspections::route('/'),
            'create' => Pages\CreateInspection::route('/create'),
            'edit' => Pages\EditInspection::route('/{record}/edit'),
        ];
    }
}
