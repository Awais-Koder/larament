<?php

namespace App\Providers;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Entry;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\Configurable;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use App\Http\Responses\RegisterResponse;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            RegistrationResponse::class,
            RegisterResponse::class
        );
    }

    protected function translatableComponents(): void
    {
        foreach ([Field::class, BaseFilter::class, Placeholder::class, Column::class, Entry::class] as $component) {
            /* @var Configurable $component */
            $component::configureUsing(function (Component $translatable): void {
                /** @phpstan-ignore method.notFound */
                $translatable->translateLabel();
            });
        }
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
    }

    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->translatableComponents();
        Filament::serving(function () {
            if (auth()->check() && auth()->user()->hasRole('storage_manager')) {
                if (auth()->user()->storage_company_id === null && request()->path() !== 'admin/storage-companies/create') {
                    // redirect('/admin/storage-companies/create')->send();
                    return;
                }
            }
        });
        // Filament::registerRenderHook('scripts.end', function () {
        //     if (request()->routeIs('filament.admin.resources.customers.index')) {
        //         return <<<'HTML'
        //             <script>
        //                 document.addEventListener('check-empty-state', () => {
        //                     setTimeout(() => {
        //                         const rows = document.querySelectorAll('[data-table-row]');
        //                         const searchInput = document.querySelector('[data-id="table-search"] input');

        //                         if (rows.length === 0 && searchInput?.value?.length) {
        //                             window.dispatchEvent(new CustomEvent('filament-notify', {
        //                                 detail: {
        //                                     title: 'No customer found',
        //                                     description: 'No flagged customer matched your search.',
        //                                     type: 'warning',
        //                                 }
        //                             }));
        //                         }
        //                     }, 500);
        //                 });
        //             </script>
        //         HTML;
        //     }
        // });
        Filament::registerRenderHook('tables::empty-state', function () {
            if (request()->routeIs('filament.admin.resources.customers.index')) {
                return view('partials.custom-empty-state-search');
            }
        });
        Filament::registerRenderHook('styles.end', function () {
            
                return <<<'HTML'
                    <style>
                        .fi-ta-filter-indicators.flex.items-start.justify-between.gap-x-3.bg-gray-50.px-3.py-1\.5.dark\:bg-white\/5.sm\:px-6 {
                            display: none!important;
                        }
                    </style>
                HTML;
            

            return null;
        });


    }

}
