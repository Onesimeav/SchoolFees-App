<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\ForgotPassword;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\AdminWelcomeWidget;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset(requestAction: ForgotPassword::class, resetAction: null)
            ->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)
            ->brandLogo(fn () => new HtmlString('
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#D97706;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="color:#fff;font-weight:700;font-size:16px;letter-spacing:.05em;">PE</span>
                    </div>
                    <span style="font-weight:600;font-size:15px;">School Admin</span>
                </div>
            '))
            ->favicon(asset('favicon-32x32.png'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AdminWelcomeWidget::class,
            ])
            ->renderHook(PanelsRenderHook::BODY_START, fn () => new HtmlString('
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
