<?php

namespace App\Filament\Resources\Evaluadors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluadorsTable
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
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
                            new \App\Mail\EvaluadorBienvenidaMail($record, $nuevaContrasena)
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
                    ->tooltip('Desactivar evaluador')
                    ->requiresConfirmation()
                    ->modalHeading('Desactivar Evaluador')
                    ->modalDescription('¿Estás seguro de que deseas desactivar a este evaluador? Perderá su acceso al sistema.')
                    ->modalSubmitActionLabel('Sí, desactivar')
                    ->visible(fn ($record) => $record->estatus === true && auth()->id() !== $record->id)
                    ->action(fn ($record) => $record->update(['estatus' => false])),
                \Filament\Actions\Action::make('activar')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Activar evaluador')
                    ->requiresConfirmation()
                    ->modalHeading('Activar Evaluador')
                    ->modalDescription('¿Estás seguro de que deseas activar a este evaluador? Recuperará su acceso al sistema.')
                    ->modalSubmitActionLabel('Sí, activar')
                    ->visible(fn ($record) => $record->estatus === false && auth()->id() !== $record->id)
                    ->action(fn ($record) => $record->update(['estatus' => true])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
