<?php

namespace App\Filament\Empresa\Pages;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use App\Filament\Resources\Empresas\Schemas\EmpresaForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

use Filament\Support\Enums\Width;

class EditProfile extends BaseEditProfile
{
    protected static ?string $title = 'Perfil de Empresa';

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
            ->components(array_merge(
                array_filter(EmpresaForm::getSchema(), function($component) {
                    if ($component instanceof \Filament\Schemas\Components\Actions) {
                        return false;
                    }
                    $name = $component->getName();
                    if ($name === 'folio') {
                        return false;
                    }
                    if ($name === 'nombre_empresa') {
                        $component->columnSpanFull();
                    }
                    return true;
                }),
                [
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
                ]
            ))
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

    protected ?string $newPassword = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['password'])) {
            $this->newPassword = $data['password'];
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    protected function afterSave(): void
    {
        // If password was updated
        if ($this->newPassword) {
            // Send email to the company's email address
            $email = $this->getUser()->correo;
            $password = $this->newPassword;

            Mail::raw("Tu contraseña ha sido actualizada exitosamente. Tu nueva contraseña es: {$password}", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Actualización de Contraseña - +Feliz');
            });

            // Reset
            $this->newPassword = null;
        }
    }
}
