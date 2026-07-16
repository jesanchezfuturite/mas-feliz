<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ConfiguradorLanding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Configuración General';
    protected static ?string $title = 'Configuración General';

    protected string $view = 'filament.pages.configurador-landing';

    public ?array $data = [];

    public function mount(): void
    {
        $globalConfig = Setting::firstOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => false]
        );

        $this->form->fill([
            'landing_partner_logo' => Setting::where('key', 'landing_partner_logo')->first()?->value,
            'flujograma_crisis' => Setting::where('key', 'flujograma_crisis')->first()?->value,
            'herramientas_empresa_activas' => (bool) $globalConfig->herramientas_empresa_activas,
        ]);
    }

    public function form(\Filament\Schemas\Schema $form): \Filament\Schemas\Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Apariencia Pública')
                    ->description('Configura los logotipos que se muestran a los usuarios.')
                    ->schema([
                        FileUpload::make('landing_partner_logo')
                            ->label('Logo secundario (Ej. Coahuila Pa\' Delante)')
                            ->disk('public')
                            ->image()
                            ->maxSize(300)
                            ->directory('logos')
                            ->helperText('Formato imagen. Tamaño máximo 300kb. Se mostrará antes del logo de +Feliz en la plataforma.'),
                    ]),

                \Filament\Schemas\Components\Section::make('Sección de Crisis')
                    ->description('Sube el flujograma de actuación ante crisis en salud mental. En cuanto lo cargues, se mostrará automáticamente en el tablero de las empresas.')
                    ->schema([
                        FileUpload::make('flujograma_crisis')
                            ->label('Flujograma de actuación ante crisis')
                            ->disk('public')
                            ->directory('crisis')
                            ->downloadable()
                            ->openable()
                            ->maxSize(51200)
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->helperText('Formato PDF o imagen. Tamaño máximo 50 MB. Mientras no haya archivo, la empresa verá un mensaje provisional.'),
                    ]),

                \Filament\Schemas\Components\Section::make('Control de Acceso')
                    ->description('Gestiona el acceso de las empresas a las herramientas de la plataforma.')
                    ->schema([
                        Toggle::make('herramientas_empresa_activas')
                            ->label('Habilitar Herramientas para Empresas')
                            ->helperText('Si está inactivo, las empresas no tendrán acceso a los menús de herramientas y solo verán un mensaje de espera en su tablero principal.'),
                    ]),

                \Filament\Schemas\Components\Actions::make([
                    \Filament\Actions\Action::make('save')
                        ->label('Guardar')
                        ->submit('save'),
                ])->alignEnd(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(
            ['key' => 'landing_partner_logo'],
            ['value' => $data['landing_partner_logo']]
        );

        Setting::updateOrCreate(
            ['key' => 'flujograma_crisis'],
            ['value' => $data['flujograma_crisis'] ?? null]
        );

        Setting::updateOrCreate(
            ['key' => 'global_config'],
            ['herramientas_empresa_activas' => (bool) ($data['herramientas_empresa_activas'] ?? false)]
        );

        Notification::make()
            ->success()
            ->title('Guardado exitosamente')
            ->send();
    }
}
