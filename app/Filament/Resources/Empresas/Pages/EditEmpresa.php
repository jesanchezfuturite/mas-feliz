<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Resources\Empresas\EmpresaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmpresa extends EditRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return array_merge(parent::getFormActions(), [
            \Filament\Actions\Action::make('reenviar_contrasena')
                ->label('Reenviar contraseña')
                ->color('primary')
                ->icon('heroicon-o-paper-airplane')
                ->extraAttributes(['class' => 'text-white', 'style' => 'color: white;'])
                ->requiresConfirmation()
                ->modalHeading('Reenviar contraseña a la Empresa')
                ->modalDescription('Se generará una nueva contraseña y se enviará por correo a la empresa. ¿Estás seguro de continuar?')
                ->action(function () {
                    $record = $this->getRecord();
                    if (! $record) return;
                    $password = \Illuminate\Support\Str::random(10);
                    $record->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);
                    \Illuminate\Support\Facades\Mail::to($record->correo)->send(new \App\Mail\AccesosTableroEmpresa($record, $password));
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Contraseña enviada por correo')
                        ->success()
                        ->send();
                })
        ]);
    }
}
