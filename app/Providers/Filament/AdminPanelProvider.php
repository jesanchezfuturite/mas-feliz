<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->brandLogo(fn () => view('filament.logo'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->colors([
                'primary' => Color::hex('#556ee6'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->defaultAvatarProvider(\App\Providers\Filament\CustomAvatarProvider::class)
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('<style>
                    .fi-sidebar { background-color: #2a3042 !important; }
                    .fi-sidebar .fi-sidebar-header { background-color: #2a3042 !important; }
                    .fi-sidebar, .fi-sidebar * { color: #a6b0cf !important; }
                    .fi-sidebar .fi-active > .fi-sidebar-item-btn { background-color: rgba(255, 255, 255, 0.1) !important; }
                    .fi-sidebar .fi-active > .fi-sidebar-item-btn, .fi-sidebar .fi-active > .fi-sidebar-item-btn * { color: #ffffff !important; font-weight: 600; }
                    .fi-sidebar .fi-sidebar-item-btn { color: #888ea8 !important; font-weight: 500 !important; padding: 0.75rem 1.25rem !important; margin: 0.25rem 0 !important; }
                    .fi-btn.fi-color-custom, .fi-btn.fi-color-primary { background-color: #556ee6 !important; padding: 0.75rem 2rem !important; height: auto !important; border-radius: 0.5rem !important; border: none !important; color: #ffffff !important; }
                    .fi-btn.fi-color-gray { padding: 0.75rem 2rem !important; height: auto !important; border-radius: 0.5rem !important; }
                    .fi-btn.fi-color-custom:hover, .fi-btn.fi-color-primary:hover { background-color: #4458b8 !important; color: #ffffff !important; }
                    .fi-btn.fi-color-custom .fi-btn-label, .fi-btn.fi-color-custom span, .fi-btn.fi-color-primary .fi-btn-label, .fi-btn.fi-color-primary span { color: #ffffff !important; }
                    body, .fi-main { background-color: #f6f6f9 !important; color: #343a40 !important; }
                    .fi-wi-stats-overview-stat, .fi-ta-ctn, .fi-fo-section, .fi-section { box-shadow: 0 0px 10px 0px var(--tw-shadow-color, #eee), 0 0px 10px 1px var(--tw-shadow-color, #eee) !important; border: none !important; ring: none !important; }
                    .section-header-icon-right .fi-section-header { display: flex !important; flex-direction: row-reverse !important; justify-content: space-between !important; width: 100% !important; align-items: center !important; }
                    .fi-ta-header { display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: space-between !important; padding: 1rem 1.5rem !important; }
                    .fi-ta-header-heading-title { color: #2a3042 !important; margin: 0 !important; }
                    .fi-ta-actions .fi-icon-btn, .fi-ta-actions .fi-icon-btn * { color: #888ea8 !important; }
                    .col-nombre-empresa * { transition: color 0.2s ease-in-out; }
                    .col-nombre-empresa:hover * { color: rgba(var(--primary-900), 1) !important; }
                    .fi-resource-create-record-page form, .fi-resource-edit-record-page form { background-color: #ffffff !important; padding: 2rem !important; border-radius: 0.75rem !important; box-shadow: 0 0px 10px 0px var(--tw-shadow-color, #eee), 0 0px 10px 1px var(--tw-shadow-color, #eee) !important; }
                    /* Modal Header Divider with Gray Background */
                    .fi-modal-header {
                        background-color: #f3f4f6 !important;
                        border-bottom: 1px solid #e2e8f0 !important;
                        padding: 1.25rem 1.5rem !important;
                        margin: 0 0 1.5rem 0 !important;
                        border-top-left-radius: 0.5rem !important;
                        border-top-right-radius: 0.5rem !important;
                    }

                    /* Custom Input Field Styles (matches Home Contact Form) */
                    .fi-input-wrapper, .fi-fo-field-wrp {
                        box-shadow: none !important;
                        --tw-shadow: 0 0 #0000 !important;
                        --tw-shadow-colored: 0 0 #0000 !important;
                        border-radius: 0.5rem !important;
                        border: 1px solid #e2e8f0 !important;
                        transition: all 150ms ease-in-out !important;
                    }
                    .fi-input-wrapper:focus-within {
                        box-shadow: 0 0 0 2px rgba(85, 110, 230, 0.5) !important; /* using primary color */
                        border-color: #556ee6 !important;
                    }
                    .fi-input, .fi-select-input {
                        padding-top: 0.625rem !important;
                        padding-bottom: 0.625rem !important;
                        padding-left: 1rem !important;
                        padding-right: 1rem !important;
                    }

                    .fi-resource-create-record-page form .fi-ac, .fi-resource-edit-record-page form .fi-ac { display: flex !important; flex-direction: row-reverse !important; justify-content: flex-start !important; gap: 1rem !important; flex-wrap: wrap !important; }
                    .fi-resource-create-record-page form .fi-btn, .fi-resource-edit-record-page form .fi-btn { padding: 0.75rem 2rem !important; height: auto !important; border-radius: 0.5rem !important; margin: 0 !important; }
                    .fi-resource-create-record-page form .fi-btn.fi-color-gray, .fi-resource-edit-record-page form .fi-btn.fi-color-gray, .fi-resource-create-record-page form .fi-btn-color-gray, .fi-resource-edit-record-page form .fi-btn-color-gray, .fi-resource-create-record-page form .fi-btn:not(.fi-color-primary):not(.fi-color-custom), .fi-resource-edit-record-page form .fi-btn:not(.fi-color-primary):not(.fi-color-custom) { background-color: #f3f4f6 !important; color: #4b5563 !important; border: none !important; box-shadow: none !important; ring: none !important; }
                    .fi-resource-create-record-page form .fi-btn.fi-color-gray:hover, .fi-resource-edit-record-page form .fi-btn.fi-color-gray:hover, .fi-resource-create-record-page form .fi-btn-color-gray:hover, .fi-resource-edit-record-page form .fi-btn-color-gray:hover, .fi-resource-create-record-page form .fi-btn:not(.fi-color-primary):not(.fi-color-custom):hover, .fi-resource-edit-record-page form .fi-btn:not(.fi-color-primary):not(.fi-color-custom):hover { background-color: #e5e7eb !important; color: #374151 !important; }
                    .fi-resource-create-record-page form .fi-btn.fi-color-gray *, .fi-resource-edit-record-page form .fi-btn.fi-color-gray *, .fi-resource-create-record-page form .fi-btn-color-gray *, .fi-resource-edit-record-page form .fi-btn-color-gray *, .fi-resource-create-record-page form .fi-btn:not(.fi-color-primary):not(.fi-color-custom) *, .fi-resource-edit-record-page form .fi-btn:not(.fi-color-primary):not(.fi-color-custom) * { color: #4b5563 !important; }
                    .fi-resource-create-record-page form .fi-btn.fi-outlined, .fi-resource-edit-record-page form .fi-btn.fi-outlined { box-shadow: none !important; border: none !important; }
                    .fi-tabs-item.fi-active { background-color: #556ee6 !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important; border-radius: 0.375rem !important; }
                    .fi-tabs-item.fi-active .fi-tabs-item-label, .fi-tabs-item.fi-active * { color: #ffffff !important; font-weight: 500 !important; }
                    div:has(> .equal-height-section) { display: flex; flex-direction: column; }
                    .equal-height-section { flex: 1; display: flex; flex-direction: column; }
                    .equal-height-section > section.fi-section { flex: 1; display: flex; flex-direction: column; }
                    .equal-height-section > section.fi-section > .fi-section-content-ctn { flex: 1; display: flex; flex-direction: column; }
                    .equal-height-section > section.fi-section > .fi-section-content-ctn > .fi-section-content { flex: 1; }
                </style>')
            );
    }
}
