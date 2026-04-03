<?php

namespace App\Providers\Filament;

use App\Filament\Staff\Pages\Auth\ForgotPassword;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Staff\Widgets\StaffWelcomeWidget;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StaffPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('staff')
            ->path('staff')
            ->login()
            ->passwordReset(requestAction: ForgotPassword::class, resetAction: null)
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#16A34A;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="color:#fff;font-weight:700;font-size:16px;letter-spacing:.05em;">PE</span>
                    </div>
                    <span style="font-weight:600;font-size:15px;">Staff Dashboard</span>
                </div>
            '))
            ->favicon(asset('favicon-32x32.png'))
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Staff/Resources'), for: 'App\Filament\Staff\Resources')
            ->discoverPages(in: app_path('Filament/Staff/Pages'), for: 'App\Filament\Staff\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Staff/Widgets'), for: 'App\Filament\Staff\Widgets')
            ->widgets([
                StaffWelcomeWidget::class,
            ])
            ->profile(\App\Filament\Staff\Pages\EditProfile::class, isSimple: false)
            ->renderHook(\Filament\View\PanelsRenderHook::BODY_START, fn () => new \Illuminate\Support\HtmlString('
                <style>
                    .fi-sidebar { border-right: 1px solid rgb(229 231 235); box-shadow: 2px 0 8px -2px rgba(0,0,0,.06); }
                    .dark .fi-sidebar { border-right-color: rgb(55 65 81); box-shadow: 2px 0 8px -2px rgba(0,0,0,.25); }
                    .fi-main-ctn { padding-left: 1rem !important; padding-right: 1rem !important; }
                </style>
            '))
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
            ]);
    }
}
