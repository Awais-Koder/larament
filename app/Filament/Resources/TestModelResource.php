<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestModelResource\Pages;
use App\Filament\Resources\TestModelResource\RelationManagers;
use App\Models\TestModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestModelResource extends Resource
{
    protected static ?string $model = TestModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListTestModels::route('/'),
            'create' => Pages\CreateTestModel::route('/create'),
            'edit' => Pages\EditTestModel::route('/{record}/edit'),
        ];
    }
}
