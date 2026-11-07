<?php

namespace App\Filament\Resources;

use App\Models\AttendanceLog;
use App\Filament\Resources\AttendanceLogResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = App\Models\AttendanceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Workforce';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return app(\App\Services\Modules\ModuleManager::class)->isActive('attendance')
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
                Forms\Components\Select::make('employee_id')
                    ->relationship(titleAttribute: 'first_name')
                    ->required(),
                Forms\Components\DatePicker::make('work_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('checked_in_at'),
                Forms\Components\DateTimePicker::make('checked_out_at'),
                Forms\Components\Select::make('method')
                    ->options(['manual'=>'Manual','mobile'=>'Mobile','qr'=>'QR','location'=>'Location','device'=>'Device']),
                Forms\Components\Select::make('status')
                    ->options(['active'=>'Active','inactive'=>'Inactive','pending'=>'Pending','confirmed'=>'Confirmed','assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled','draft'=>'Draft','unpaid'=>'Unpaid','paid'=>'Paid','open'=>'Open','failed'=>'Failed','excellent'=>'Excellent','good'=>'Good','needs_improvement'=>'Needs Improvement','full_time'=>'Full Time','part_time'=>'Part Time','present'=>'Present','one_time'=>'One Time','recurring'=>'Recurring','scheduled'=>'Scheduled']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('work_date')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('checked_in_at')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('method')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
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
            'index' => Pages\ListAttendanceLogs::route('/'),
            'create' => Pages\CreateAttendanceLog::route('/create'),
            'edit' => Pages\EditAttendanceLog::route('/{record}/edit'),
        ];
    }
}
