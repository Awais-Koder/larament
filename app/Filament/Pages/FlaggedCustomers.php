<?php

// namespace App\Filament\Pages;

// use App\Models\Customer;
// use Filament\Pages\Page;
// use Filament\Tables;
// use Filament\Tables\Columns\TextColumn;
// use Illuminate\Database\Eloquent\Builder;
// use Filament\Tables\Contracts\HasTable;


// class FlaggedCustomers extends Page implements HasTable
// {
//     use Tables\Concerns\InteractsWithTable;

//     protected static ?string $navigationIcon = 'heroicon-o-flag';

//     protected static string $view = 'filament.pages.flagged-customers';

//     protected static ?string $navigationLabel = 'Flagged Customers';

//     protected static ?string $navigationGroup = 'Customers';

//     protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
//     {
//         return \App\Models\Customer::query()
//             ->whereHas('flaggedReasons')
//             ->with(['facility', 'flaggedReasons']);
//     }



//     protected function getTableColumns(): array
//     {
//         return [
//         Tables\Columns\TextColumn::make('name')
//             ->label('Name')
//             ->searchable(),
//         Tables\Columns\TextColumn::make('email')
//             ->label('Email'),
//         Tables\Columns\TextColumn::make('phone')
//             ->label('Phone'),
//         Tables\Columns\TextColumn::make('facility.name')
//             ->label('Facility'),
//         Tables\Columns\TextColumn::make('flaggedReasons.0.reason')
//             ->label('Reason')
//             ->limit(50),
//     ];
//     }
//     protected function getTableActions(): array
//     {
//         return [
//         Tables\Actions\Action::make('unflag')
//             ->label('Unflag')
//             ->color('success')
//             ->icon('heroicon-o-check-circle')
//             ->requiresConfirmation()
//             ->action(function ($record) {
//                 $record->flaggedReasons()->delete();
//             }),
//     ];
//     }

//     public static function canAccess(): bool
//     {
//         return auth()->user()?->hasRole('super_admin') || auth()->user()?->hasRole('storage_manager');
//     }
// }
