<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageCompanyResource\Pages;
use App\Filament\Resources\StorageCompanyResource\RelationManagers;
use App\Models\StorageCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class StorageCompanyResource extends Resource
{
    protected static ?string $model = StorageCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function shouldRegisterNavigation(): bool
    {
        return true; // Hides from the sidebar
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
       
             ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()
                ->formatStateUsing(function ($state, $record) {
                    $isVerified = $record->user?->is_verified ?? false;

                    $icon = $isVerified
                        ? '<img src="' . asset('images/verified-user.svg') . '" alt="Verified" class="w-4 h-4 inline ml-1">'
                        : '';

                    return $state . $icon;
                })->html(),
                    // ->formatStateUsing(fn ($state, $record) => $record->is_flagged ? "🚩 $state" : $state),
                       //->getStateUsing(fn ($record) => $record->is_flagged ? "🚩 {$record->name}" : $record->name),
                TextColumn::make('email')->searchable(),
                //TextColumn::make('phone'),
                //TextColumn::make('facility.name')->label('Facility'),
                // TextColumn::make('flagged_reason')
                //     ->label('Reason')
                //     // ->formatStateUsing(fn ($state, $record) => $record->is_flagged ? $record->flagged_reason : null)
                //     ->getStateUsing(fn ($record) => $record->is_flagged ? $record->flagged_reason : null)
                //     ->limit(50)
                //     ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        auth()->check() &&
                        (
                            auth()->user()->hasRole('super_admin') ||
                            $record->facility?->storage_company_id === auth()->user()->storage_company_id
                        )
                    ),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) =>
                        auth()->check() &&
                        (
                            auth()->user()->hasRole('super_admin') ||
                            $record->facility?->storage_company_id === auth()->user()->storage_company_id
                        )
                    ),
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
            'index' => Pages\ListStorageCompanies::route('/'),
            'create' => Pages\CreateStorageCompany::route('/create'),
            'edit' => Pages\EditStorageCompany::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        if (auth()->check()) {
            if (auth()->user()->hasRole('super_admin')) {
                return parent::getEloquentQuery();
            }

            return parent::getEloquentQuery()
                ->where('id', auth()->user()->storage_company_id);
        }

        // Optional: handle guest users more gracefully
        return parent::getEloquentQuery()->whereRaw('0 = 1');
        
    }
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

}
