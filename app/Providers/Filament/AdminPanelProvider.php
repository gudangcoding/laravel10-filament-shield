<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Http\Middleware\TenantMiddleware;
use App\Models\Team;
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
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Plugin;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        // $rolePlugin = \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make();
        // $tenantMenu = true;
        // $roles = auth()->user()->roles->pluck('name')->first();
        // if (!auth()->check() || !auth()->user()->roles(['admin', 'super_admin'])) {
        //     $rolePlugin = null;
        //     $tenantMenu = false;
        // }
        // $roles = auth()->user()->roles->pluck('name')->first();


        $rolePlugin = \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make();
        $tenantMenu = true;

        if (auth()->check()) {
            $user = auth()->user();
            $roles = $user->roles->pluck('name')->first();

            // Ubah kondisi berdasarkan peran yang diperlukan
            if ($roles === 'admin' || $roles === 'super_admin') {
                // Lakukan sesuatu jika pengguna memiliki peran 'admin' atau 'super_admin'
                // Misalnya, set variabel $rolePlugin ke instance FilamentShieldPlugin
                $rolePlugin = new FilamentShieldPlugin;
                $tenantMenu = true;
            } else {
                // Lakukan sesuatu jika pengguna tidak memiliki peran yang diperlukan
                // Misalnya, set variabel $rolePlugin menjadi null dan nonaktifkan menu tenant
                $rolePlugin = null;
                $tenantMenu = false;
            }
        } else {
            // Lakukan sesuatu jika pengguna tidak terotentikasi
            // Misalnya, set variabel $rolePlugin menjadi null dan nonaktifkan menu tenant
            $rolePlugin = null;
            $tenantMenu = false;
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop(true)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,


            ])
            ->plugins([
                $rolePlugin ?? new FilamentShieldPlugin,

            ])
            ->tenant(Team::class, ownershipRelationship: 'team', slugAttribute: 'slug')
            ->tenantMenu(
                TenantMiddleware::class,
                // $roles === 'super_admin' ? true : false,
            )
            ->tenantRegistration(RegisterTeam::class)
            ->tenantProfile(EditTeamProfile::class)
            ->brandName('PT SAJ')
            ->databaseNotifications()
            // ->databaseNotifications()
            // ->brandLogo(fn () => view('filament.admin.logo'))
            // ->brandLogo(asset('images/logo.svg'))
        ;
    }
}
