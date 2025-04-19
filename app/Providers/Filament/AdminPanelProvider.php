<?php

namespace App\Providers\Filament;

use App\Enums\UserRoleEnum;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\EditProfile;
use App\Filament\Resources\UserResource;
use App\Http\Middleware\CustomizeAdminPanelMiddleware;
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use FilipFonal\FilamentLogManager\FilamentLogManager;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

//        Auth::user()->settings['admin_panel_color'] ? Auth::user()->settings['admin_panel_color'] :
//        function () use ($current_color) {
//
//            return [
//                'primary' => $current_color,
//            ];
////                fn()=> Auth::user()->settings['admin_panel_color'] ? Color::{Str::title(Auth::user()->settings['admin_panel_color'])} : $current_color,
//        }
//            )
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->registration(Register::class)
            ->login(action: \App\Filament\Pages\Auth\Login::class)
            ->profile(EditProfile::class,false)
            ->colors (fn ()=> [
                'primary' => match(true){
                    Str::startsWith( Request::getPathInfo(), '/gold')=> Color::Amber,
                    Str::startsWith( Request::getPathInfo(), '/silver')=> Color::Stone,
                    default => Color::Green
                }
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
                FilamentSpatieLaravelHealthPlugin::make()
                    ->authorize(fn () => Auth::user()->role == UserRoleEnum::Admin),
                FilamentLogManager::make(),
                QuickCreatePlugin::make()
                    ->excludes([
                        UserResource::class
                    ])
                    ->alwaysShowModal(),
                FilamentJobsMonitorPlugin::make(),
            ])
            ->passwordReset()
//            ->brandLogo(logo: asset("storage/bandit.png"))
            ->brandName(config('app.name'))
            ->maxContentWidth(MaxWidth::Full)
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->breadcrumbs(false)
            ->sidebarFullyCollapsibleOnDesktop()
            ->spa()
            ->topNavigation(fn()=> Auth::user()->settings['enable_top_navbar'])
            ;
    }
}
