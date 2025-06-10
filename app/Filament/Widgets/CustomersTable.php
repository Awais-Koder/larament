<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use App\Filament\Resources\CustomerResource;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Textarea;


class CustomersTable extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $title = 'Clients';
    
    public function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->heading($user && $user->hasRole('storage_manager') ? 'Find A Flagged Tenant' : 'Find A Flagged Customers')
            ->emptyStateHeading('Please search for a customer')
            ->emptyStateDescription('You can search by name, email, phone, or RSA ID.')
            ->emptyStateIcon('heroicon-m-magnifying-glass')
            ->query(Customer::query()->latest())
            ->searchDebounce(300)
            ->searchPlaceholder('Search by RSA ID or Email...')
            ->columns([
                IconColumn::make('is_flagged')
                    ->label('Status')
                    ->tooltip(fn ($record) =>
                        $record->flaggedReasons()->latest()->first()?->action_type === 'flagged'
                            ? 'Currently Flagged'
                            : 'Currently Unflagged'
                    )
                    ->boolean()
                    ->color(fn ($record) => $record->flaggedReasons()->latest()->first()?->action_type === 'flagged' ? 'danger' : 'success')
                    ->icon(fn ($record) => $record->flaggedReasons()->latest()->first()?->action_type === 'flagged'
                        ? 'heroicon-o-flag'
                        : 'heroicon-o-check-circle')
                    ->state(fn ($record) => $record->flaggedReasons()->latest()->first()?->action_type === 'flagged'),
                TextColumn::make('name')
                    // ->grow()
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                // TextColumn::make('facility.name')->label('StorageFacility'),
                TextColumn::make('rsa_id')->label('RSA ID')->searchable(),
                
                TextColumn::make('flagged_reason')
                    ->label('Flagged Reason')
                    // ->formatStateUsing(fn ($state, $record) => $record->is_flagged ? $record->flagged_reason : null)
                    ->state(fn ($record) =>
                        $record->is_flagged && filled($record->flagged_reason)
                            ? $record->flagged_reason
                            : '—'
                    )
                    ->limit(50)
                    ->wrap(),
                //TextColumn::make('is_flagged')->label('flagged')->searchable(),
            ])
            ->modifyQueryUsing(function (Builder $query, HasTable $livewire) {
                $user = auth()->user();

                if (! $user || ! $user->hasRole('storage_manager')) {
                    return $query;
                }

                $search = $livewire->getTableSearch();

                if (empty($search)) {
                    return $query->whereNull('id'); // No search, show nothing
                }

                return $query
                    ->where('is_flagged', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('email', $search)
                        ->orWhere('rsa_id', $search);
                    });
            })
            ->filters([
                // Tables\Filters\TernaryFilter::make('is_flagged')
                //     ->label('Flagged Status')
                //     ->trueLabel('Only flagged')
                //     ->falseLabel('Only unflagged')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('is_flagged', true),
                //         false: fn (Builder $query) => $query->where('is_flagged', false),
                //     ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        auth()->check() && (
                            auth()->user()->hasRole('super_admin') || 
                            //$record->facility?->storage_company_id === auth()->user()->storage_company_id
                            $record->user_id === auth()->id()
                        )
                    ),

                Tables\Actions\Action::make('toggle-flag')
                    ->label(fn ($record) =>
                        optional($record->flaggedReasons()->latest()->first())->action_type === 'flagged'
                            ? 'Unflag'
                            : 'Flag'
                    )
                    ->color(fn ($record) =>
                        optional($record->flaggedReasons()->latest()->first())->action_type === 'flagged'
                            ? 'success'
                            : 'danger'
                    )
                    ->icon(fn ($record) =>
                        optional($record->flaggedReasons()->latest()->first())->action_type === 'flagged'
                            ? 'heroicon-o-check-circle'
                            : 'heroicon-o-flag'
                    )
                    ->form(fn ($record) => [
                        Textarea::make('reason')
                            ->label(optional($record->flaggedReasons()->latest()->first())->action_type === 'flagged'
                                ? 'Why are you unflagging this customer?'
                                : 'Flag Reason')
                            ->placeholder(optional($record->flaggedReasons()->latest()->first())->action_type === 'flagged'
                                ? 'Explain what changed or why this issue is resolved...'
                                : 'Describe why this customer is being flagged.')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $user = auth()->user();

                        $latestActionType = optional($record->flaggedReasons()->latest()->first())->action_type;
                        $isCurrentlyFlagged = $latestActionType === 'flagged';

                        $newState = ! $isCurrentlyFlagged;

                        $record->update([
                            'is_flagged' => $newState,
                            'flagged_reason' => $data['reason'] ?? null,
                        ]);

                        \App\Models\FlaggedCustomerReason::create([
                            'customer_id' => $record->id,
                            'action_by' => $user->id,
                            'action_type' => $newState ? 'flagged' : 'unflagged',
                            'reason' => $data['reason'] ?? ($newState ? 'Flagged without reason' : 'Unflagged'),
                        ]);

                        return $record->refresh();
                    })
                    ->successNotificationTitle(fn ($record) =>
                        optional($record->flaggedReasons()->latest()->first())->action_type === 'flagged'
                            ? 'Customer flagged successfully'
                            : 'Customer unflagged successfully'
                    ),
                    
            ]);
    }
    
    public function updatedTableSearch(): void
    {
        $search = $this->getTableSearch();

        if (
            auth()->user()?->hasRole('storage_manager') &&
            filled($search)
        ) {
            $found = \App\Models\Customer::query()
                ->where('is_flagged', 1)
                ->where(function ($query) use ($search) {
                    $query->where('rsa_id', $search)
                            ->orWhere('email', $search);
                })
                ->exists();

            if (! $found) {
                Notification::make()
                    ->title('No customer found with the provided RSA ID or email.')
                    ->warning()
                    // ->persistent()
                    ->send();
            }
            if ($found) {
                Notification::make()
                    ->title('Customer found!')
                    ->success()
                    ->persistent()
                    ->send();
            }
        }
    }
}
