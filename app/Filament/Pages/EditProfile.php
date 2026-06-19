<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\Width;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'Perfil de Usuario';

    public static function isSimple(): bool
    {
        return false;
    }

    public function getBreadcrumbs(): array
    {
        return [
            filament()->getCurrentPanel()->getUrl() => 'Escritorio',
            '' => 'Perfil',
        ];
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'fi-custom-profile-page',
        ];
    }

    public function defaultForm(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return parent::defaultForm($schema)->inlineLabel(false);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent()
                    ->label('Nombres'),
                \Filament\Forms\Components\TextInput::make('apellidos')
                    ->label('Apellidos')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(false),
                \Filament\Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono')
                    ->required()
                    ->maxLength(255),
                
                \Filament\Forms\Components\Placeholder::make('separator')
                    ->hiddenLabel()
                    ->content(new \Illuminate\Support\HtmlString('<div style="margin-top: 2rem; margin-bottom: 1rem; border-top: 1px solid #e5e7eb; padding-top: 2rem;"><h3 style="font-size: 1.5rem; font-weight: 600; color: #111827;">Cambiar Contraseña</h3><p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Opcional: Llena estos campos solo si deseas cambiar tu contraseña.</p></div>'))
                    ->columnSpanFull(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                \Filament\Forms\Components\Placeholder::make('spacer')
                    ->hiddenLabel()
                    ->content(new \Illuminate\Support\HtmlString('<div style="height: 2rem;"></div>'))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected function getPasswordConfirmationFormComponent(): \Filament\Schemas\Components\Component
    {
        return \Filament\Forms\Components\TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::auth/pages/edit-profile.form.password_confirmation.label'))
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->requiredWith('password')
            ->dehydrated(false);
    }
}
