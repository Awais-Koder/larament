<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use App\Models\FlaggedCustomerReason;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $title = 'Clients';

    public function getHeading(): string
    {
        auth()->check();
        $user = auth()->user();

        if ($user && $user->hasRole('storage_manager')) {
            return 'Tenants Table';
        }

        return 'Customers Table';
    }
    
    public static function getLabel(): string
    {
        if (! auth()->check()) {
            return 'Customer'; // fallback if no user is authenticated
        }

        return auth()->user()->hasRole('storage_manager') ? 'Tenant' : 'Customer';
    }

    public static function getPluralLabel(): string
    {
        if (! auth()->check()) {
            return 'Customers'; // fallback if no user is authenticated
        }

        return auth()->user()->hasRole('storage_manager') ? 'My Tenants' : 'My Customers';
    }

    public static function getNavigationLabel(): string
    {
        return static::getPluralLabel();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hides from the sidebar
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
                // ->searchable()
                ->unique(ignoreRecord: true),

            TextInput::make('phone')
                ->required()
                ->tel()
                ->maxLength(20),

            TextInput::make('rsa_id')
                ->label('RSA ID')
                ->required()
                ->tel()
                ->maxLength(13)
                ->minLength(13)
                ->unique(ignoreRecord: true),

            Toggle::make('if_flagged')
                ->label('Flagged')
                ->default(false)
                ->reactive()
                ->required(),

            Textarea::make('flagged_reason')
                ->label('Flag Reason')
                ->visible(fn (callable $get) => $get('is_flagged'))
                ->required(fn (callable $get) => $get('is_flagged')),

            TextInput::make('user_id')->hidden(),

            ])
            ->columns(1);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('You do not have any tenants yet')
            // ->emptyStateDescription('You can add a new tenant by clicking the "New Tenant" button at the top right of this screen.')
            ->emptyStateIcon('heroicon-m-magnifying-glass')
            ->emptyStateActions([
                Action::make('add-tenant')
                    ->label('Add Tenant')
                    ->icon('heroicon-m-plus-circle')
                    ->button()
                    ->color('primary')
                    ->url(route('filament.admin.resources.customers.create'))
                    ->openUrlInNewTab(false),
                ])
            ->query(Customer::query()->latest())
            ->searchDebounce(300)
            ->searchPlaceholder('Search...')
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

                // Only show customers that belong to the current user
                return $query->where('user_id', $user->id);
            })
            ->filters([
                // RSA ID filter
                Filter::make('rsa_id')
                    ->form([
                        TextInput::make('rsa_id')->label('RSA ID'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['rsa_id'])) {
                            $query->where('rsa_id', $data['rsa_id']);
                        }
                    }),

                // Email filter
                Filter::make('email')
                    ->form([
                        TextInput::make('email')->label('Email'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['email'])) {
                            $query->where('email', $data['email']);
                        }
                    }),

                // Tables\Actions\Action::make('toggle-flag')
                //     ->label(fn ($record) => $record->is_flagged ? 'Unflag' : 'Flag')
                //     ->color(fn ($record) => $record->is_flagged ? 'success' : 'danger')
                //     ->icon(fn ($record) => $record->is_flagged
                //         ? 'heroicon-o-check-circle'
                //         : 'heroicon-o-flag')
                //     ->visible(function ($record) {
                //         if (!auth()->check()) return false;

                //         if (!$record->is_flagged) {
                //             return true; // Any logged-in user can flag
                //         }

                //         $user = auth()->user();

                //         return $user->hasRole('super_admin') ||
                //             $record->facility?->storage_company_id === $user->storage_company_id;
                //     })
                //     ->requiresConfirmation() // optional if you want a simple "Are you sure?" instead of a modal with a form
                //     ->action(function ($record) {
                //         $user = auth()->user();

                //         if (
                //             $record->is_flagged &&
                //             !$user->hasRole('super_admin') &&
                //             $record->facility?->storage_company_id !== $user->storage_company_id
                //         ) {
                //             abort(403, 'You are not authorized to unflag this customer.');
                //         }

                //         $isNowFlagged = !$record->is_flagged;

                //         $record->update([
                //             'is_flagged' => $isNowFlagged,
                //             'flagged_reason' => null,
                //         ]);

                //         \App\Models\FlaggedCustomerReason::create([
                //             'customer_id' => $record->id,
                //             'action_by' => $user->id,
                //             'action_type' => $isNowFlagged ? 'flagged' : 'unflagged',
                //             'reason' => $isNowFlagged ? 'Flagged (no reason)' : 'Unflagged',
                //         ]);

                //         return $record->refresh();
                //     })
                //     ->successNotificationTitle(fn ($record) =>
                //         $record->is_flagged ? 'Customer unflagged successfully' : 'Customer flagged successfully'
                // ),
            ])
            ->actions([

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        auth()->check() && (
                            auth()->user()->hasRole('super_admin',) ||
                            //$record->facility?->storage_company_id === auth()->user()->storage_company_id
                            $record->user_id === auth()->user()->id
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
                        Forms\Components\Textarea::make('reason')
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
                
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('flag')
                    ->label('Flag selected')
                    ->icon('heroicon-o-flag')
                    ->color('danger')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if (!$record->is_flagged) {
                                $record->update([
                                    'is_flagged' => true,
                                    'flagged_reason' => 'Bulk flagged',
                                ]);

                                FlaggedCustomerReason::create([
                                    'customer_id' => $record->id,
                                    'action_by' => auth()->id(),
                                    'action_type' => 'flagged',
                                    'reason' => 'Bulk flagged',
                                ]);
                            }
                        }
                    }),

                Tables\Actions\BulkAction::make('unflag')
                    ->label('Unflag selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            if ($record->is_flagged) {
                                $record->update([
                                    'is_flagged' => false,
                                    'flagged_reason' => null,
                                ]);

                                FlaggedCustomerReason::create([
                                    'customer_id' => $record->id,
                                    'action_by' => auth()->id(),
                                    'action_type' => 'unflagged',
                                    'reason' => 'Bulk unflagged',
                                ]);
                            }
                        }
                    }),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('flaggedReasons');

        $user = auth()->user();

        if (! $user) {
            return $query->whereNull('id'); // No results for unauthenticated users
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }
        // If editing a specific record (e.g., URL is /customers/{id}/edit), we must allow that record through
        $editingId = request()->route('record');

        return $query->where(function ($q) use ($user, $editingId) {
            $q->where('user_id', $user->id)
            ->orWhere('id', $editingId); // ✅ allow access if it's the record being edited
        });

        // $search = request()->query('tableSearch');

        // if (! $search) {
        //     return $query->whereNull('id'); // Default: show nothing
        // }

        // return $query
        //     ->where(function ($subQuery) use ($search) {
        //         $subQuery
        //             ->where('email', $search)
        //             ->orWhere('rsa_id', $search);
        //     });
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //     ->where($is_flagged = 1);
    // }
    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('storage_manager');
    }
    

}
