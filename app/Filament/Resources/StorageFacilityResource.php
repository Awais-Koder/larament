<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageFacilityResource\Pages;
use App\Filament\Resources\StorageFacilityResource\RelationManagers;
use App\Models\StorageFacility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StorageFacilityResource extends Resource
{
    protected static ?string $model = StorageFacility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'My Facility';
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hides from the sidebar
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStorageFacilities::route('/'),
            'create' => Pages\CreateStorageFacility::route('/create'),
            'edit' => Pages\EditStorageFacility::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        if (!auth()->check()) {
            return parent::getEloquentQuery()->whereRaw('1 = 0'); // Return nothing if not authenticated
        }

        if (auth()->user()->hasRole('super_admin')) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()
            ->whereHas('customer.facility', function ($query) {
                $query->where('storage_company_id', auth()->user()->storage_company_id);
        });
    }
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

}
