<?php

namespace App\Filament\Resources;

use App\Models\Payment;
use App\Filament\Resources\PaymentResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = App\Models\Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return app(\App\Services\Modules\ModuleManager::class)->isActive('payments')
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
                Forms\Components\Select::make('invoice_id')
                    ->relationship(titleAttribute: 'number')
                    ->required(),
                Forms\Components\Select::make('customer_id')
                    ->relationship(titleAttribute: 'name')
                    ->required(),
                Forms\Components\Select::make('method')
                    ->required()
                    ->options(['manual'=>'Manual','mobile'=>'Mobile','qr'=>'QR','location'=>'Location','device'=>'Device']),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(['active'=>'Active','inactive'=>'Inactive','pending'=>'Pending','confirmed'=>'Confirmed','assigned'=>'Assigned','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled','draft'=>'Draft','unpaid'=>'Unpaid','paid'=>'Paid','open'=>'Open','failed'=>'Failed','excellent'=>'Excellent','good'=>'Good','needs_improvement'=>'Needs Improvement','full_time'=>'Full Time','part_time'=>'Part Time','present'=>'Present','one_time'=>'One Time','recurring'=>'Recurring','scheduled'=>'Scheduled']),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('paid_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('method')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('amount')->sortable(),
                Tables\Columns\TextColumn::make('paid_at')->searchable()->sortable(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
