<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlaggedCustomerReasonResource\Pages;
use App\Filament\Resources\FlaggedCustomerReasonResource\RelationManagers;
use App\Models\FlaggedCustomerReason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlaggedCustomerReasonResource extends Resource
{
    protected static ?string $model = FlaggedCustomerReason::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected function afterSave(): void
    {
        $data = $this->form->getState();

        $wasFlagged = $this->record->getOriginal('is_flagged');
        $isNowFlagged = $this->record->is_flagged;

        if ($wasFlagged !== $isNowFlagged) {
            FlaggedCustomerReason::create([
                'customer_id' => $this->record->id,
                'reason' => $this->record->flagged_reason ?? 'No reason provided',
                'action_by' => auth()->id(),
                'action_type' => $isNowFlagged ? 'flagged' : 'unflagged',
            ]);
        }
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
            'index' => Pages\ListFlaggedCustomerReasons::route('/'),
            'create' => Pages\CreateFlaggedCustomerReason::route('/create'),
            'edit' => Pages\EditFlaggedCustomerReason::route('/{record}/edit'),
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
