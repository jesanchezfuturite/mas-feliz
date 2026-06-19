<?php

namespace App\Filament\Resources;

use App\Models\Autoevaluacion;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use App\Filament\Empresa\Resources\Autoevaluacions\Schemas\AutoevaluacionForm;

class AutoevaluacionResource extends Resource
{
    protected static ?string $model = Autoevaluacion::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        // Re-use the exact same form from the Empresa panel
        return AutoevaluacionForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'view' => \App\Filament\Resources\Autoevaluacions\Pages\ViewAutoevaluacion::route('/{record}'),
        ];
    }
}
