<?php

namespace App\Filament\Empresa\Pages;

use App\Filament\Resources\Empresas\Schemas\ReconocimientoForm;
use App\Models\Empresa;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;

/**
 * Donde la empresa confirma los datos de su reconocimiento.
 *
 * Angélica (31/10/2026): al dictaminar, que la plataforma le pregunte a la
 * empresa si su nombre es correcto —y si no, que lo cambie—, le pida el
 * logotipo en HD y los contactos para la invitación; «que sea hasta un
 * candado para ver el resultado». Mientras no confirme, la tarjeta del
 * dictamen del escritorio manda aquí en vez de enseñar el nivel.
 */
class DatosReconocimiento extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Mi reconocimiento';

    protected static ?string $title = 'Datos de tu reconocimiento';

    protected static ?string $slug = 'reconocimiento';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.empresa.pages.datos-reconocimiento';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /**
     * Solo para quien obtuvo el distintivo: a nadie más se le entrega un
     * reconocimiento. No depende del interruptor de herramientas porque,
     * igual que el dictamen, esto no es una herramienta.
     */
    public static function canAccess(): bool
    {
        $empresa = auth()->user();

        return $empresa instanceof Empresa && $empresa->estaAprobada();
    }

    public function mount(): void
    {
        $this->form->fill(ReconocimientoForm::valores($this->empresa()));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...ReconocimientoForm::getSchema(),

                Actions::make([
                    Action::make('save')
                        ->label(fn () => $this->empresa()->reconocimientoPendiente()
                            ? 'Confirmar y ver mi resultado'
                            : 'Guardar cambios')
                        ->submit('save'),
                ])->alignEnd(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $empresa = $this->empresa();
        $estabaPendiente = $empresa->reconocimientoPendiente();

        $empresa->update(ReconocimientoForm::datos($this->form->getState()));

        Notification::make()
            ->success()
            ->title($estabaPendiente ? 'Datos confirmados' : 'Cambios guardados')
            ->body($estabaPendiente
                ? 'Gracias. Ya puedes consultar el resultado de tu dictaminación.'
                : 'Los datos de tu reconocimiento quedaron actualizados.')
            ->send();

        $this->redirect(filament()->getCurrentPanel()->getUrl());
    }

    protected function getViewData(): array
    {
        $empresa = $this->empresa();

        return [
            'pendiente' => $empresa->reconocimientoPendiente(),
            'confirmado' => $empresa->reconocimiento_confirmado_at,
        ];
    }

    private function empresa(): Empresa
    {
        return auth()->user();
    }
}
