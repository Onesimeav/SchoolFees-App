<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Portal\Pages\Auth\ForgotPassword;
use App\Filament\Portal\Pages\Auth\Login;
use App\Filament\Portal\Pages\Auth\Register;
use App\Filament\Portal\Pages\Dashboard;
use App\Filament\Portal\Pages\EditProfile;
use Filament\Enums\ThemeMode;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('portal')
            ->path('portal')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(
                requestAction: ForgotPassword::class,
                resetAction: null,
            )
            ->brandLogo(fn () => new \Illuminate\Support\HtmlString('
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#2563EB;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="color:#fff;font-weight:700;font-size:16px;letter-spacing:.05em;">PE</span>
                    </div>
                    <span style="font-weight:600;font-size:15px;">Portail Étudiant</span>
                </div>
            '))
            ->favicon(asset('favicon-32x32.png'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->darkMode()
            ->defaultThemeMode(ThemeMode::System)
            ->profile(EditProfile::class, isSimple: false)
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\Filament\Portal\Resources')
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\Filament\Portal\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Portal/Widgets'), for: 'App\Filament\Portal\Widgets')
            ->widgets([])
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
