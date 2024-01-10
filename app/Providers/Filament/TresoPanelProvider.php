<?php

namespace App\Providers\Filament;

use App\Http\Middleware\Auth;
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

class TresoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('treso')
            ->path('treso')
            ->brandName("Pic'Asso - Trésorerie")
            ->colors(
                [
                'primary' => Color::Blue,
                'danger' => Color::Red,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                ]
            )
            ->discoverResources(in: app_path('Filament/Treso/Resources'), for: 'App\\Filament\\Treso\\Resources')
            ->discoverPages(in: app_path('Filament/Treso/Pages'), for: 'App\\Filament\\Treso\\Pages')
            ->pages(
                [
                Pages\Dashboard::class,
                ]
            )
            ->discoverWidgets(in: app_path('Filament/Treso/Widgets'), for: 'App\\Filament\\Treso\\Widgets')
            ->widgets(
                [
                Widgets\AccountWidget::class,
                ]
            )
            ->middleware(
                [
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ]
            )
            ->authMiddleware(
                [
                Auth::class,
                ]
            );
    }
}
