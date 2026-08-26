<?php

namespace App\Providers\Filament;

use App\Http\Middleware\CheckRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Panel del Gestor: trabajador social que da seguimiento a las gestiones de
 * canalización y asigna las citas de las personas referidas a Salud.
 */
class GestorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('gestor')
            ->path('gestor')
            ->login()
            ->brandName('+Feliz - Gestión')
            ->brandLogo(fn () => view('filament.evaluador-logo'))
            ->brandLogoHeight('5rem')
            ->favicon(asset('images/favicon.png'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Gestor/Resources'), for: 'App\\Filament\\Gestor\\Resources')
            ->discoverPages(in: app_path('Filament/Gestor/Pages'), for: 'App\\Filament\\Gestor\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Gestor/Widgets'), for: 'App\\Filament\\Gestor\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->defaultAvatarProvider(CustomAvatarProvider::class)
            ->darkMode(false)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckRole::class.':gestor',
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => view('franja-ambiente-prueba')->render()
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('<div class="mr-3 font-medium text-sm text-gray-700 dark:text-gray-200">{{ auth()->user()->name }} {{ auth()->user()->apellidos }}</div>')
            )
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn (): string => view('filament.footer-logos')->render()
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('<style>
                    .fi-sidebar { background-color: #2a3042 !important; }
                    .fi-sidebar .fi-sidebar-header { background-color: #2a3042 !important; }
                    .fi-sidebar, .fi-sidebar * { color: #a6b0cf !important; }
                    .fi-sidebar .fi-active > .fi-sidebar-item-btn { background-color: rgba(255, 255, 255, 0.1) !important; }
                    .fi-sidebar .fi-active > .fi-sidebar-item-btn, .fi-sidebar .fi-active > .fi-sidebar-item-btn * { color: #ffffff !important; font-weight: 600; }
                    .fi-sidebar .fi-sidebar-item-btn { color: #888ea8 !important; font-weight: 500 !important; padding: 0.75rem 1.25rem !important; margin: 0.25rem 0 !important; }
                    .fi-sidebar .fi-sidebar-item-btn:hover { background-color: rgba(255, 255, 255, 0.1) !important; }
                    .fi-sidebar .fi-sidebar-item-btn:hover, .fi-sidebar .fi-sidebar-item-btn:hover * { color: #ffffff !important; font-weight: 600; }
                    .fi-btn.fi-color-custom, .fi-btn.fi-color-primary { background-color: #d97706 !important; padding: 0.75rem 2rem !important; height: auto !important; border-radius: 0.5rem !important; border: none !important; color: #ffffff !important; }
                    .fi-btn.fi-color-gray { padding: 0.75rem 2rem !important; height: auto !important; border-radius: 0.5rem !important; }
                    .fi-btn.fi-color-custom:hover, .fi-btn.fi-color-primary:hover { background-color: #b45309 !important; color: #ffffff !important; }
                    .fi-btn.fi-color-custom .fi-btn-label, .fi-btn.fi-color-custom span, .fi-btn.fi-color-primary .fi-btn-label, .fi-btn.fi-color-primary span { color: #ffffff !important; }
                    body, .fi-main { background-color: #f6f6f9 !important; color: #343a40 !important; }
                    .fi-wi-stats-overview-stat, .fi-ta-ctn, .fi-fo-section, .fi-section { box-shadow: 0 0px 10px 0px var(--tw-shadow-color, #eee), 0 0px 10px 1px var(--tw-shadow-color, #eee) !important; border: none !important; ring: none !important; }
                    .fi-ta-header { display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; padding: 1rem 1.5rem !important; }
                    .fi-ta-header-heading-title { color: #2a3042 !important; margin: 0 !important; }
                    .fi-ta-actions .fi-icon-btn, .fi-ta-actions .fi-icon-btn * { color: #888ea8 !important; }
                    .fi-resource-create-record-page form, .fi-resource-edit-record-page form { background-color: #ffffff !important; padding: 2rem !important; border-radius: 0.75rem !important; box-shadow: 0 0px 10px 0px var(--tw-shadow-color, #eee), 0 0px 10px 1px var(--tw-shadow-color, #eee) !important; }
                    .fi-input-wrapper, .fi-fo-field-wrp { border-radius: 0.5rem !important; border: 1px solid #e2e8f0 !important; }
                    .fi-input-wrapper:focus-within { border-color: #d97706 !important; ring: 1px solid #d97706 !important; }
                    .fi-topbar, .fi-topbar-header { background-color: #d97706 !important; }
                </style>')
            );
    }
}
