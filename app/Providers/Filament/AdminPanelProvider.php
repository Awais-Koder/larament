<?php

namespace App\Providers\Filament;

use App\Filament\Pages\App\Profile;
use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
// use Filament\Pages\Auth\Register;
use Filament\Pages\Auth\Register;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use App\Http\Responses\RegisterResponse;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Filament\Support\Facades\FilamentAsset;


//use App\Filament\Widgets\CustomersTable;


class AdminPanelProvider extends PanelProvider
{
    
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->navigationItems([
                NavigationItem::make('profile')
                    ->label('My Profile')
                    ->url('/admin/profile')
                    ->icon('heroicon-o-user')
                    ->group('Manage'),

                NavigationItem::make('tenants-or-customers')
                    ->label(fn () => auth()->user()?->hasRole('storage_manager') ? 'My Tenants' : 'My Customers')
                    ->url('/admin/customers')
                    ->icon(fn () => auth()->user()?->hasRole('storage_manager') ? 'heroicon-o-users' : 'heroicon-o-building-office-2')
                    ->group('Manage'),
            ])
            // ->login(Login::class)
            // ->registration(Register::class)
            ->registration()
            ->login()
            //->registration(\App\Filament\Pages\Auth\Register::class)
            //->login(Login::class)
            // ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('images/logo-final.png'))
            ->brandLogoHeight('4rem')
            ->favicon(asset('images/favicon.png'))
//            ->sidebarFullyCollapsibleOnDesktop()
            ->spa()
            ->profile(Profile::class, false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                // 'primary' => Color::Blue,
                'primary' => '#3e88c9',
            ])
            ->globalSearch(false)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            //->globalSearchFieldKeyBindingSuffix()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
                //Pages\Dashboard::class,
                //\App\Filament\Pages\RegisterPage::class, 
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                //Widgets\CustomersTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            // ->plugin(
            //     \Jeffgreco13\FilamentBreezy\BreezyCore::make()
            //         ->myProfile()
            //         ->enableTwoFactorAuthentication(
            //             force: false,
            //         ),

            // )
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
    public function boot(): void
    {
        Filament::serving(function () {
            // if (! auth()->check()) {
            //     return;
            // }

            // $user = auth()->user();

            // //if (auth()->check() && ! auth()->user()->is_verified) {
            // if (! $user->is_verified) {
            //     //abort(403, 'Your account has been deactivated, please contact admin@storagecheck.co.za.');
            //     abort(response()->view('errors.account-pending', [], 403));
            // }
            if (! auth()->check()) {
                return;
            }

            // Allow the verification route to process without interruption
            if (request()->is('email/verify/*')) {
                return;
            }

            $user = auth()->user();

            if (! $user->is_verified) {
                abort(response()->view('errors.account-pending', [], 403));
            }
        });
        // FilamentAsset::pushStyle('app-theme', asset('build/assets/theme-6JhuZSus.css'));

        // FilamentAsset::registerStyles([
        //     'app-theme' => asset('build/assets/theme-6JhuZSus.css'), // <-- insert real hashed name here
        // ]);
    }
    
}
