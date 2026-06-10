<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ConfiguradorLanding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Configurador landing';
    protected static ?string $title = 'Configurador Landing';

    protected string $view = 'filament.pages.configurador-landing';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'landing_partner_logo' => Setting::where('key', 'landing_partner_logo')->first()?->value,
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
                        
                        \Filament\Schemas\Components\Actions::make([
                            \Filament\Actions\Action::make('save')
                                ->label('Guardar')
                                ->submit('save'),
                        ])->alignEnd(),
                    ]),
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

        Notification::make()
            ->success()
            ->title('Guardado exitosamente')
            ->send();
    }
}
