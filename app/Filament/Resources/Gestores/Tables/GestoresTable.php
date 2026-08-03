<?php

namespace App\Filament\Resources\Gestores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GestoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->getStateUsing(fn ($record) => $record->name . ' ' . $record->apellidos)
                    ->searchable(['name', 'apellidos']),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                TextColumn::make('telefono')
                    ->searchable(),
                \Filament\Tables\Columns\IconColumn::make('estatus')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Editar'),
                \Filament\Actions\Action::make('reenviar_credenciales')
                    ->label('Reenviar')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Reenviar credenciales')
                    ->requiresConfirmation()
                    ->modalHeading('Reenviar Credenciales')
                    ->modalDescription(fn ($record) => 'Se enviará un nuevo correo de acceso a ' . $record->email . '. Por seguridad, se generará una nueva contraseña.')
                    ->modalSubmitActionLabel('Sí, reenviar')
                    ->action(function ($record) {
                        $nuevaContrasena = \Illuminate\Support\Str::password(10);
                        $record->update(['password' => \Illuminate\Support\Facades\Hash::make($nuevaContrasena)]);
                        \Illuminate\Support\Facades\Mail::to($record->email)->send(
                            new \App\Mail\GestorBienvenidaMail($record, $nuevaContrasena)
                        );
                        \Filament\Notifications\Notification::make()
                            ->title('Credenciales reenviadas con éxito')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('desactivar')
                    ->label('Desactivar')
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Desactivar gestor')
                    ->requiresConfirmation()
                    ->modalHeading('Desactivar Gestor')
                    ->modalDescription('¿Estás seguro de que deseas desactivar a este gestor? Perderá su acceso al sistema.')
                    ->modalSubmitActionLabel('Sí, desactivar')
                    ->visible(fn ($record) => $record->estatus === true)
                    ->action(fn ($record) => $record->update(['estatus' => false])),
                \Filament\Actions\Action::make('activar')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Activar gestor')
                    ->requiresConfirmation()
                    ->modalHeading('Activar Gestor')
                    ->modalSubmitActionLabel('Sí, activar')
                    ->visible(fn ($record) => $record->estatus === false)
                    ->action(fn ($record) => $record->update(['estatus' => true])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
