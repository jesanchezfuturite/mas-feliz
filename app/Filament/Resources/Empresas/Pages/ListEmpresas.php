<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Resources\Empresas\EmpresaResource;
use App\Filament\Resources\Empresas\Widgets\EmpresaStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $iconHtml = \Filament\Support\generate_icon_html('heroicon-m-building-office', attributes: new \Illuminate\View\ComponentAttributeBag(['style' => 'width: 2rem; height: 2rem; color: #2a3042;']))->toHtml();
        return new \Illuminate\Support\HtmlString('<div style="display: flex; align-items: center; gap: 0.75rem;">' . $iconHtml . ' <span>Empresas</span></div>');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes(['class' => 'btn-crear-empresa']),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EmpresaStats::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            EmpresaStats::class,
        ];
    }
}
