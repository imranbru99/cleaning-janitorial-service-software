<?php

namespace App\Filament\Resources;

use App\Models\InventoryItem;
use App\Filament\Resources\InventoryItemResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryItemResource extends Resource
{
    protected static ?string $model = App\Models\InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        return app(\App\Services\Modules\ModuleManager::class)->isActive('inventory')
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
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('sku'),
                Forms\Components\TextInput::make('category'),
                Forms\Components\TextInput::make('quantity_on_hand')
                    ->numeric(),
                Forms\Components\TextInput::make('reorder_level')
                    ->numeric(),
                Forms\Components\TextInput::make('unit_cost')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('quantity_on_hand')->sortable(),
                Tables\Columns\TextColumn::make('reorder_level')->searchable()->sortable(),
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
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
